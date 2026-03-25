<?php

namespace Lucinda\MVC\EventListener;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\Facet;

/**
 * Requires that the EventListener will return a Facet
 */
interface Faceted extends EventListener
{
    /**
     * Executes event listener logic and returns created Facet
     * 
     * @return Facet
     */
    function run(): Facet;
}