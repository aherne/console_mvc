<?php

namespace Lucinda\ConsoleSTDOUT;

/**
 * Encapsulates transport layer that collects variables to be passed through API objects
 */
class Attributes
{
    private string $requestedRoute;
    private string $requestedResponseFormat;

    /**
     * Sets requested route detected by matching original requested to XML directives
     *
     * @param string $route
     */
    public function setValidRoute(string $route): void
    {
        $this->requestedRoute = $route;
    }

    /**
     * Gets requested route detected by matching original requested to XML directives
     *
     * @example UsersSync
     * @return string
     */
    public function getValidRoute(): string
    {
        return $this->requestedRoute;
    }

    /**
     * Gets requested response format detected by matching original to XML directives
     *
     * @param string $format
     */
    public function setValidFormat(string $format): void
    {
        $this->requestedResponseFormat = $format;
    }

    /**
     * Gets requested response format detected by matching original requested to XML directives
     *
     * @example text
     * @return string
     */
    public function getValidFormat(): string
    {
        return $this->requestedResponseFormat;
    }
}
