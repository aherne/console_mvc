<?php

namespace Lucinda\MVC\EventListener;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\Runnable;

/**
 * Requires that the EventListener will return a Runnable
 */
interface UnFaceted extends EventListener, Runnable
{
}