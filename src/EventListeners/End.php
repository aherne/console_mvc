<?php

namespace Lucinda\ConsoleSTDOUT\EventListeners;

use Lucinda\MVC\Runnable;
use Lucinda\ConsoleSTDOUT\Attributes;
use Lucinda\ConsoleSTDOUT\Application;
use Lucinda\ConsoleSTDOUT\Request;
use Lucinda\MVC\Response;

/**
 * Defines blueprint of an event that executes when application ends execution (after response is committed to client)
 */
abstract class End implements Runnable
{
    protected Attributes $attributes;
    protected Application $application;
    protected Request $request;
    protected Response $response;

    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes  $attributes
     * @param Application $application
     * @param Request     $request
     * @param Response    $response
     */
    public function __construct(
        Attributes $attributes,
        Application $application,
        Request $request,
        Response $response
    ) {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->response = $response;
    }
}
