<?php
namespace Test\Lucinda\MVC\Response;

use Lucinda\MVC\Response\Status;
use Lucinda\UnitTest\Result;

class StatusTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new Status(404);
    }

    public function getId()
    {
        return new Result($this->object->getId()==404);
    }
        

    public function getDescription()
    {
        return new Result($this->object->getDescription()=="Not Found");
    }
}
