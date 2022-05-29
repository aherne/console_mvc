<?php

namespace Lucinda\ConsoleSTDOUT\EventListeners;

use Lucinda\ConsoleSTDOUT\RouteNotFoundException;
use Lucinda\MVC\ConfigurationException;

/**
 * Validates request data based on Application and Request objects and saves results to Attributes
 */
class RequestValidator extends Request
{
    /**
     * Performs request validation
     * @throws RouteNotFoundException|ConfigurationException
     */
    public function run(): void
    {
        $this->attributes->setValidRoute($this->getValidRoute());
        $this->attributes->setValidFormat($this->getValidFormat());
    }

    /**
     * Sets valid route requested by user
     *
     * @throws RouteNotFoundException
     * @return string
     */
    private function getValidRoute(): string
    {
        $route = $this->request->getRoute();
        if ($route=="") {
            $route = $this->application->getDefaultRoute();
        }
        if ($this->application->routes($route)===null) {
            throw new RouteNotFoundException("Route could not be matched to routes.route tag @ XML: ".$route);
        }
        return $route;
    }

    /**
     * Sets valid format to use in response for current request
     *
     * @throws ConfigurationException
     * @return string
     */
    private function getValidFormat(): string
    {
        $extension = $this->application->getDefaultFormat();
        $route = $this->application->routes($this->application->getDefaultRoute());
        if ($route->getFormat()) {
            $extension = $route->getFormat();
        }
        if ($this->application->resolvers($extension)===null) {
            throw new ConfigurationException("Format could not be matched to resolvers.resolver tag @ XML: ".$extension);
        }
        return $extension;
    }
}
