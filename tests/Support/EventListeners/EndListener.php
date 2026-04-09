<?php

namespace Test\Lucinda\ConsoleSTDOUT\Support\EventListeners;

use Lucinda\MVC\EventListener\UnFaceted;

class EndListener implements UnFaceted
{
    public function run(): void
    {
        file_put_contents((string) getenv("FC_EVENT_LOG"), "end\n", FILE_APPEND);
    }
}
