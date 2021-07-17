<?php
namespace Test\Lucinda\MVC\Application;

use Lucinda\MVC\Application\Route;
use Lucinda\UnitTest\Result;

class RouteTest
{
    private $object;
    
    
    public function __construct()
    {
        $this->object = new Route(simplexml_load_string('
      	<route id="user/(name)" controller="BlogController" view="blog" format="json" method="GET"/>
        '));
    }
    
    public function getID()
    {
        return new Result($this->object->getID()=="user/(name)");
    }

    public function getController()
    {
        return new Result($this->object->getController()=="BlogController");
    }
        

    public function getView()
    {
        return new Result($this->object->getView()=="blog");
    }
    
    public function getFormat()
    {
        return new Result($this->object->getFormat()=="json");
    }
}
