<?php

namespace Lucinda\MVC\EventListener;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\FacetCollection;

/**
 * Requires that the EventListener will return a FacetCollection
 */
interface MultiFaceted extends EventListener
{
    /**
     * Executes event listener logic and returns created FacetCollection
     * 
     * @return FacetCollection
     */
    function run(): FacetCollection;
}