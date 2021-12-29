<?php
namespace Lucinda\ConsoleSTDOUT\EventListeners;

use Lucinda\ConsoleSTDOUT\Application;
use Lucinda\ConsoleSTDOUT\Attributes;
use Lucinda\MVC\Runnable;

/**
 * Defines blueprint of an event that executes after request that came from client is parsed into a Request object
 */
abstract class Request implements Runnable
{
    protected Attributes $attributes;
    protected Application $application;
    protected \Lucinda\ConsoleSTDOUT\Request $request;

    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param Application $application
     * @param \Lucinda\ConsoleSTDOUT\Request $request
     */
    public function __construct(Attributes $attributes, Application $application, \Lucinda\ConsoleSTDOUT\Request $request)
    {
        $this->attributes = $attributes;
        $this->application = $application;
        $this->request = $request;
    }
}
