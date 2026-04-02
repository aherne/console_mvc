<?php

namespace Lucinda\ConsoleSTDOUT;

use Lucinda\MVC\Runnable;
use Lucinda\MVC\Response\View;
use Lucinda\MVC\EventScheduler;
use Lucinda\MVC\EventType;
use Lucinda\MVC\FacetRegistry;
use Lucinda\MVC\ReflectionInjector;
use Lucinda\MVC\TerminationException;
use Lucinda\ConsoleSTDOUT\Request\Validator as ValidatedRequest;
use Lucinda\MVC\Controller\ViewAware;
use Lucinda\MVC\EventListener\Faceted;
use Lucinda\MVC\EventListener\MultiFaceted;
use Lucinda\MVC\Response\Console;
use Lucinda\MVC\Response\Transformer\Body as TransformerBody;
use Lucinda\MVC\Service\ResolverInfoDetector;
use Lucinda\MVC\Service\ViewDetector;
use Lucinda\MVC\XmlTags\ResolverInfo;

/**
 * Implements STDOUT front controller MVC functionality, integrating all API components as a whole.
 */
class FrontController implements Runnable
{
    protected string $documentDescriptor;
    protected FacetRegistry $facetRegistry;
    protected ReflectionInjector $reflectionInjector;
    protected EventScheduler $eventScheduler;

    /**
     * Starts API front controller, setting up necessary variables
     *
     * @param Attributes $attributes
     * @param string     $documentDescriptor
     */
    public function __construct(
        string $documentDescriptor,
        EventScheduler $eventScheduler
    )
    {
        // saves arguments
        $this->documentDescriptor = $documentDescriptor;
        $this->facetRegistry = new FacetRegistry();
        $this->reflectionInjector = new ReflectionInjector($this->facetRegistry);
        $this->eventScheduler = $eventScheduler;
    }

    /**
     * Performs all steps required to convert request to response in procedural mode, while delegating to subcomponents,
     * to maximize performance
     */
    public function run(): void
    {
        try {
            // execute events for START
            $this->runEvents(EventType::START);

            // reads XML configuration file
            $application = new Application($this->documentDescriptor);
            $this->facetRegistry->put($application->getApplicationInfo());

            // execute events for APPLICATION
            $this->runEvents(EventType::APPLICATION);

            // reads user request, into request (RO) object
            $request = new Request();
            $this->facetRegistry->put($request);

            // validate request
            $requestValidator = new ValidatedRequest($application, $request);
            $this->facetRegistry->put($requestValidator);

            // execute events for REQUEST
            $this->runEvents(EventType::REQUEST);

            // determine response format
            $responseInfoDetector = new ResolverInfoDetector($application, $requestValidator);
            $response = new Console();

            // locates and runs page controller and sets up view
            $filledView = $this->runController($application, $requestValidator);
            $viewDetector = new ViewDetector($application, $requestValidator, $filledView);
            $view = $viewDetector->getView();

            // resolves view into response body, unless output stream has been written to already
            if ($view !== false) {
                $this->runViewResolver($responseInfoDetector->getResolver(), $response, $view);
            }

            // execute events for RESPONSE
            $this->runResponseTransformers($response);

            // commits response to caller
            $response->run();

            // execute events for END            
            $this->runEvents(EventType::END);
        } catch (TerminationException $e) {
            $e->getResponse()->run();
        }
    }

    /**
     * Executes all event listeners set to be run before any handling of request
     *
     * @return void
     */
    protected function runEvents(EventType $eventType): void
    {
        $eventsToRun = $this->eventScheduler->get($eventType);      
        foreach ($eventsToRun as $className) {
            $object = $this->reflectionInjector->create($className);
            if ($object instanceof Faceted) {
                $this->facetRegistry->put($object->run());
            } else if ($object instanceof MultiFaceted) {
                $facets = $object->run();
                foreach ($facets as $id=>$facet) {
                    $this->facetRegistry->putAs($id, $facet);
                }
            } else {
                $object->run();
            }
        }
    }

    /**
     * Executes all response listeners that in turn transform the response
     * 
     * @param Console $response
     */
    protected function runResponseTransformers(Console $response): void
    {
        $eventsToRun = $this->eventScheduler->get(EventType::RESPONSE);
        foreach ($eventsToRun as $className) {
            $object = $this->reflectionInjector->create($className);
            if ($object instanceof TransformerBody) {
                $response->transformBody($object);
            }
        }
    }

    /**
     * Detects and executes page controller, if any
     *
     * @param Application $application
     * @param ValidatedRequest $validatedRequest
     * @return ?View
     */
    protected function runController(
        Application $application,
        ValidatedRequest $validatedRequest
    ): ?View
    {
        if ($className  = $application->getRoutes($validatedRequest->getRoute())->getController()) {
            $object = $this->reflectionInjector->create($className);
            if ($object instanceof ViewAware) {
                return $object->run();
            } else {
                $object->run();
            }
        }
        return null;
    }

    /**
     * Detects resolver to compile view into response body, if not already written
     *
     * @param ResolverInfo $resolverInfo
     * @param Console $response
     * @param View $view
     * @return void
     */
    protected function runViewResolver(
        ResolverInfo $resolverInfo,
        Console $response,
        View $view
    ): void {
        $resolver = $this->reflectionInjector->create($resolverInfo->getViewResolver());
        $response->resolve($view, $resolver);
    }
}
