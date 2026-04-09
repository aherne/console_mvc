<?php
namespace Test\Lucinda\ConsoleSTDOUT\Request;

use Lucinda\ConsoleSTDOUT\Application;
use Lucinda\ConsoleSTDOUT\Request;
use Lucinda\ConsoleSTDOUT\Request\Validator;
use Lucinda\UnitTest\Validator\Strings;

class ValidatorTest
{
    private function createValidator(array $argv): Validator
    {
        $_SERVER["argv"] = $argv;

        return new Validator(
            new Application(__DIR__."/../fixtures/validator.xml"),
            new Request()
        );
    }

    public function getRoute(): array
    {
        return [
            (new Strings($this->createValidator(["index.php"])->getRoute()))->assertEquals("index"),
            (new Strings($this->createValidator(["index.php", "test"])->getRoute()))->assertEquals("test")
        ];
    }

    public function getFormat(): array
    {
        return [
            (new Strings($this->createValidator(["index.php"])->getFormat()))->assertEquals("txt"),
            (new Strings($this->createValidator(["index.php", "test"])->getFormat()))->assertEquals("json")
        ];
    }
}
