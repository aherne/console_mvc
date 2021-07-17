<?php
use Lucinda\MVC\Application;
use Lucinda\MVC\Application\Format;
use Lucinda\MVC\Application\Route;

class ApplicationMock extends Application
{
    public function __construct($xmlFilePath)
    {
        $this->readXML($xmlFilePath);
        $this->setApplicationInfo();
        $this->setResolvers();
        $this->setRoutes();
    }
}
