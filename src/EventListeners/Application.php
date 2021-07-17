<?php
namespace Lucinda\ConsoleSTDOUT\EventListeners;

use Lucinda\ConsoleSTDOUT\Attributes;
use Lucinda\MVC\Runnable;

/**
 * Defines blueprint of an event that executes after XML that contains application settings is parsed
 */
abstract class Application implements Runnable
{
    /**
     * @var Attributes
     */
    protected $attributes;

    /**
     * @var \Lucinda\ConsoleSTDOUT\Application
     */
    protected $application;


    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     * @param \Lucinda\ConsoleSTDOUT\Application $application
     */
    public function __construct(Attributes $attributes, \Lucinda\ConsoleSTDOUT\Application $application)
    {
        $this->application = $application;
        $this->attributes = $attributes;
    }
}
