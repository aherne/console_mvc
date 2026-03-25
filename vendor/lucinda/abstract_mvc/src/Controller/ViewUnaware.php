<?php

namespace Lucinda\MVC\Controller;

use Lucinda\MVC\Controller;
use Lucinda\MVC\Runnable;

/**
 * Requires that the outcome of the controller will not hydrate any view
 */
interface ViewUnaware extends Controller, Runnable
{
}
