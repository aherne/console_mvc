<?php

namespace Test\Lucinda\ConsoleSTDOUT\Support\EventListeners;

use Lucinda\MVC\EventListener\UnFaceted;

class StartListener implements UnFaceted
{
    public function run(): void
    {
        file_put_contents((string) getenv("FC_EVENT_LOG"), "start\n", FILE_APPEND);
    }
}
