<?php

namespace Lucinda\ConsoleSTDOUT\Request;

use Lucinda\ConsoleSTDOUT\RouteNotFoundException;
use Lucinda\ConsoleSTDOUT\Request;
use Lucinda\MVC\Application;
use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Facet;
use Lucinda\MVC\RequestValidator;

/**
 * Validates request data based on Application and Request objects and saves results to Attributes
 */
final class Validator implements Facet, RequestValidator
{
    private string $route;
    private string $format;

    public function __construct(Application $application, Request $request)
    {
        $this->setRoute($application, $request);
        $this->setFormat($application);
    }

    /**
     * Sets valid route requested by user
     *
     * @throws RouteNotFoundException
     * @return string
     */
    private function setRoute(Application $application, Request $request): void
    {
        $route = $request->getRoute();
        if ($route=="") {
            $route = $application->getApplicationInfo()->getDefaultRoute();
        }
        if ($application->getRoutes($route)===null) {
            throw new RouteNotFoundException("Route could not be matched to routes.route tag @ XML: ".$route);
        }
        $this->route = $route;
    }

    /**
     * Gets validated route
     * 
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Sets valid format to use in response for current request
     *
     * @throws ConfigurationException
     * @return string
     */
    private function setFormat(Application $application): string
    {
        $extension = $application->getApplicationInfo()->getDefaultFormat();
        $route = $application->getRoutes($application->getApplicationInfo()->getDefaultRoute());
        if ($route->getFormat()) {
            $extension = $route->getFormat();
        }
        if ($application->getResolvers($extension)===null) {
            throw new ConfigurationException("Format could not be matched to resolvers.resolver tag @ XML: ".$extension);
        }
        return $extension;
    }

    /**
     * Gets validated format
     * 
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}
