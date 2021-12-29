<?php
namespace Lucinda\ConsoleSTDOUT\EventListeners;

use Lucinda\MVC\Runnable;
use Lucinda\ConsoleSTDOUT\Attributes;

/**
 * Defines blueprint of an event that executes when application starts execution (before XML is read)
 */
abstract class Start implements Runnable
{
    protected Attributes $attributes;

    /**
     * Saves objects to be available in implemented run() methods.
     *
     * @param Attributes $attributes
     */
    public function __construct(Attributes $attributes)
    {
        $this->attributes = $attributes;
    }
}
