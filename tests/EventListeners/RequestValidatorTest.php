<?php
namespace Test\Lucinda\ConsoleSTDOUT\EventListeners;

use Lucinda\ConsoleSTDOUT\EventListeners\RequestValidator;
use Lucinda\ConsoleSTDOUT\Attributes;
use Lucinda\ConsoleSTDOUT\Application;
use Lucinda\ConsoleSTDOUT\Request;
use Lucinda\UnitTest\Result;

class RequestValidatorTest
{
    public function run()
    {
        $_SERVER = [
            'argv' => ["index.php", "test", "me"]
        ];
        $attributes = new Attributes();
        $validator = new RequestValidator($attributes, new Application(dirname(__DIR__)."/mocks/configuration.xml"), new Request());
        $validator->run();

        $results = [];
        $results[] = new Result($attributes->getValidRoute()=="test", "getValidRoute");
        $results[] = new Result($attributes->getValidFormat()=="txt", "getValidFormat");

        return $results;
    }
}
