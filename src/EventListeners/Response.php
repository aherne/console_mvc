<?php
namespace Lucinda\ConsoleSTDOUT\EventListeners;

use Lucinda\MVC\Runnable;
use Lucinda\ConsoleSTDOUT\Attributes;
use Lucinda\ConsoleSTDOUT\Application;
use Lucinda\ConsoleSTDOUT\Request;

/**
 * Defines blueprint of an event that executes before response is rendered to client
 */
abstract class Response implements Runnable
{
    /**
     * @var Attributes
     */
    protected $attributes;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Lucinda\MVC\Response
     */
    protected $response;


    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param Request $request
     * @param \Lucinda\MVC\Response $response
     */
    public function __construct(Attributes $attributes, Application $application, Request $request, \Lucinda\MVC\Response $response)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->response = $response;
    }
}
