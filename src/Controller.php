<?php
namespace Lucinda\ConsoleSTDOUT;

use Lucinda\MVC\Runnable;
use Lucinda\MVC\Response;

/**
 * Defines an abstract controller.
 */
abstract class Controller implements Runnable
{
    protected Application $application;
    protected Request $request;
    protected Response $response;
    protected Attributes $attributes;

    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Attributes $attributes, Application $application, Request $request, Response $response)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
        $this->response = $response;
    }
}
