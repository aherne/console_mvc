<?php

namespace Lucinda\MVC\Controller;

use Lucinda\MVC\Controller;
use Lucinda\MVC\Response\View;

/**
 * Requires that the outcome of the controller will hydrate a View 
 */
interface ViewAware extends Controller
{
    /**
     * Executes controller and returns a view.
     * 
     * @return View
     */
    function run(): View;
}
