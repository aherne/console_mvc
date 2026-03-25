<?php

namespace Lucinda\MVC;

use Lucinda\MVC\Response\Basic;

/**
 * Terminates execution with a response
 */
final class TerminationException extends \RuntimeException
{
    private Response $response;

    /**
     * Sets response to terminate execution with
     * 
     * @param Response $response
     */
    public function __construct(Response $response) {
        if ($response instanceof Basic) {
            throw new ConfigurationException("Basic responses should not be terminated abruptly!")
        }
        $this->response = $response;
    }

    /**
     * Gets response to terminate with
     * 
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}