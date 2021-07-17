<?php
namespace Test\Lucinda\ConsoleSTDOUT;

use Test\Lucinda\ConsoleSTDOUT\mocks\TestAttributes;
use Lucinda\ConsoleSTDOUT\FrontController;
use Lucinda\ConsoleSTDOUT\EventType;
use Test\Lucinda\ConsoleSTDOUT\mocks\EventListeners\EndTracker;
use Test\Lucinda\ConsoleSTDOUT\mocks\EventListeners\StartTracker;
use Lucinda\UnitTest\Result;

class FrontControllerTest
{
    private $object;
    private $attributes;

    public function __construct()
    {
        $this->attributes = new TestAttributes();
        $this->object = new FrontController(__DIR__."/mocks/configuration.xml", $this->attributes);
    }

    public function addEventListener()
    {
        $this->object->addEventListener(EventType::START, StartTracker::class);
        $this->object->addEventListener(EventType::END, EndTracker::class);
        return new Result(true);
    }


    public function run()
    {
        $_SERVER = [
            'argv' => ["index.php", "test", "me"]
        ];
        ob_start();
        $this->object->run();
        $response = ob_get_contents();
        ob_clean();

        $results = [];
        $results[] = new Result($response=="Test: me", "tested response");
        $results[] = new Result($this->attributes->getStartTime() && $this->attributes->getEndTime() && $this->attributes->getEndTime()>$this->attributes->getStartTime(), "tested event listeners");
        return $results;
    }
}
