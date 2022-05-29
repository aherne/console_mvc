<?php

namespace Lucinda\ConsoleSTDOUT;

use Lucinda\MVC\ConfigurationException;

/**
 * Compiles information about application.
 */
class Application extends \Lucinda\MVC\Application
{
    /**
     * Populates attributes based on an XML file
     *
     * @param string $xmlFilePath XML file url
     * @throws ConfigurationException If xml content has failed validation.
     */
    public function __construct(string $xmlFilePath)
    {
        $this->readXML($xmlFilePath);
        $this->setApplicationInfo();
        $this->setRoutes();
        $this->setResolvers();
    }
}
