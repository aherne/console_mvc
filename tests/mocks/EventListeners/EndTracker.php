<?php
namespace Test\Lucinda\ConsoleSTDOUT\mocks\EventListeners;

use Lucinda\ConsoleSTDOUT\EventListeners\End;

class EndTracker extends End
{
    /**
     * @var \Test\Lucinda\ConsoleSTDOUT\mocks\TestAttributes
     */
    protected $attributes;

    public function run(): void
    {
        $this->attributes->setEndTime();
    }
}
