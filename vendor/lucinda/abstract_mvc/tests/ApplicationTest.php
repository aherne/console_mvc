<?php
namespace Test\Lucinda\MVC;

use Lucinda\UnitTest\Result;

require_once("mocks/ApplicationMock.php");

class ApplicationTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new \ApplicationMock(__DIR__."/mocks/configuration.xml");
    }

    public function getDefaultFormat()
    {
        return new Result($this->object->getDefaultFormat()=="json");
    }
        

    public function getDefaultRoute()
    {
        return new Result($this->object->getDefaultRoute()=="index");
    }        

    public function getViewsPath()
    {
        return new Result($this->object->getViewsPath()=="tests/mocks");
    }
        

    public function getVersion()
    {
        return new Result($this->object->getVersion()=="1.0.0");
    }
        

    public function resolvers()
    {
        return new Result(sizeof($this->object->resolvers())==1);
    }
        

    public function routes()
    {
        return new Result(sizeof($this->object->routes())==2);
    }
        

    public function getTag()
    {
        return new Result($this->object->getTag("application")!==null);
    }
        

    public function getXML()
    {
        return new Result($this->object->getXML() instanceof \SimpleXMLElement);
    }
}
