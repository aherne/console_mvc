<?php
namespace Lucinda\ConsoleSTDOUT;

use Lucinda\MVC\Runnable;
use Lucinda\MVC\Response;
use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Application\Format;

/**
 * Implements STDOUT front controller MVC functionality, integrating all API components as a whole.
 */
class FrontController implements Runnable
{
    private $documentDescriptor;
    private $attributes;
    private $events = [];
    
    /**
     * Starts API front controller, setting up necessary variables
     *
     * @param Attributes $attributes
     * @param string $documentDescriptor
     */
    public function __construct(string $documentDescriptor, Attributes $attributes)
    {
        // saves arguments
        $this->documentDescriptor = $documentDescriptor;
        $this->attributes = $attributes;
        
        // initialize events
        $this->events = [
            EventType::START=>[],
            EventType::APPLICATION=>[],
            EventType::REQUEST=>["\\Lucinda\\ConsoleSTDOUT\\EventListeners\\RequestValidator"],
            EventType::RESPONSE=>[],
            EventType::END=>[]
        ];
    }
    
    /**
     * Adds an event listener
     *
     * @param string $type One of EventType enum values
     * @param string $className Name of event listener class (including namespace and subfolder, if any)
     */
    public function addEventListener(string $type, string $className): void
    {
        $this->events[$type][] = $className;
    }
    
    /**
     * Performs all steps required to convert request to response in procedural mode, while delegating to subcomponents, to maximize performance
     *
     * @throws RouteNotFoundException If an invalid route was requested from client or setup by developer in XML.
     * @throws ConfigurationException If any other situation where execution cannot continue.
     */
    public function run(): void
    {
        // execute events for START
        foreach ($this->events[EventType::START] as $className) {
            $runnable = new $className($this->attributes);
            $runnable->run();
        }
        
        // reads XML configuration file
        $application = new Application($this->documentDescriptor);
        
        // execute events for APPLICATION
        foreach ($this->events[EventType::APPLICATION] as $className) {
            $runnable = new $className($this->attributes, $application);
            $runnable->run();
        }
        
        // reads user request, into request (RO), session (RW) and cookies (RW) objects
        $request = new Request();
        
        // execute events for REQUEST
        foreach ($this->events[EventType::REQUEST] as $className) {
            $runnable = new $className($this->attributes, $application, $request);
            $runnable->run();
        }
        
        // initializes response
        $format = $application->resolvers($this->attributes->getValidFormat());
        $response = new Response($this->getContentType($format), $this->getTemplateFile($application));
        
        // locates and runs page controller
        $className  = $application->routes($this->attributes->getValidRoute())->getController();
        if ($className) {
            $runnable = new $className($this->attributes, $application, $request, $response);
            $runnable->run();
        }
        
        // resolves view into response body, unless output stream has been written to already
        if ($response->getBody()===null) {
            $className  = $format->getViewResolver();
            $runnable = new $className($application, $response);
            $runnable->run();
        }
        
        // execute events for RESPONSE
        foreach ($this->events[EventType::RESPONSE] as $className) {
            $runnable = new $className($this->attributes, $application, $request, $response);
            $runnable->run();
        }
        
        // commits response to caller
        $response->commit();
        
        // execute events for END
        foreach ($this->events[EventType::END] as $className) {
            $runnable = new $className($this->attributes, $application, $request, $response);
            $runnable->run();
        }
    }
    
    /**
     * Gets response template file
     *
     * @param Application $application
     * @return string
     */
    private function getTemplateFile(Application $application): string
    {
        $template = $application->routes($this->attributes->getValidRoute())->getView();
        return ($template?$application->getViewsPath()."/".$template:"");
    }
    
    /**
     * Gets response content type
     *
     * @param Format $format
     * @return string
     */
    private function getContentType(Format $format): string
    {
        $charset = $format->getCharacterEncoding();
        return $format->getContentType().($charset?"; charset=".$charset:"");
    }
}
