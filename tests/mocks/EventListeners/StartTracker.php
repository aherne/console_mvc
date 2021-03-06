<?php

namespace Test\Lucinda\ConsoleSTDOUT\mocks\EventListeners;

use Lucinda\ConsoleSTDOUT\EventListeners\Start;

class StartTracker extends Start
{
    /**
     * @var \Test\Lucinda\ConsoleSTDOUT\mocks\TestAttributes
     */
    protected \Lucinda\ConsoleSTDOUT\Attributes $attributes;

    public function run(): void
    {
        $this->attributes->setStartTime();
    }
}
