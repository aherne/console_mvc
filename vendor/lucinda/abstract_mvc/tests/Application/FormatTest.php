<?php
namespace Test\Lucinda\MVC\Application;

use Lucinda\MVC\Application\Format;
use Lucinda\UnitTest\Result;

class FormatTest
{
    private $object;
    
    
    public function __construct()
    {
        $this->object = new Format(simplexml_load_string('
        <format name="html" content_type="text/html" class="ViewLanguageResolver" charset="UTF-8"/>
        '));
    }
    
    public function getName()
    {
        return new Result($this->object->getName()=="html");
    }
    
    
    public function getContentType()
    {
        return new Result($this->object->getContentType()=="text/html");
    }
    
    
    public function getCharacterEncoding()
    {
        return new Result($this->object->getCharacterEncoding()=="UTF-8");
    }
    
    
    public function getViewResolver()
    {
        return new Result($this->object->getViewResolver()=="ViewLanguageResolver");
    }
}
