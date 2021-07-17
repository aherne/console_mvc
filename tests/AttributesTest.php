<?php
namespace Test\Lucinda\ConsoleSTDOUT;

use Lucinda\UnitTest\Result;
use Lucinda\ConsoleSTDOUT\Attributes;

class AttributesTest
{
    private $object;

    public function __construct()
    {
        $this->object = new Attributes();
    }


    public function setValidRoute()
    {
        $this->object->setValidRoute("test");
        return new Result(true);
    }


    public function getValidRoute()
    {
        return new Result($this->object->getValidRoute()=="test");
    }

    public function setValidFormat()
    {
        $this->object->setValidFormat("txt");
        return new Result(true);
    }


    public function getValidFormat()
    {
        return new Result($this->object->getValidFormat()=="txt");
    }
}
