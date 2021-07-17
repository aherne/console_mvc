<?php
namespace Lucinda\MVC;

use Lucinda\MVC\Application\Format;
use Lucinda\MVC\Application\Route;

/**
 * Detects settings necessary to configure MVC API based on contents of XML file
 */
class Application
{
    protected $simpleXMLElement;
    
    protected $viewsPath;
    
    protected $defaultFormat;
    protected $defaultRoute;
    
    protected $version;
    
    protected $routes=array();
    protected $formats=array();
    
    protected $objectsCache=array();
    
    /**
     * Reads XML supplied
     *
     * @param string $xmlFilePath Relative location of XML file containing settings.
     * @throws ConfigurationException If XML is misconfigured.
     */
    protected function readXML(string $xmlFilePath)
    {
        if (!file_exists($xmlFilePath)) {
            throw new ConfigurationException("XML file not found: ".$xmlFilePath);
        }
        $this->simpleXMLElement = simplexml_load_file($xmlFilePath);
    }
    
    /**
     * Sets basic application info based on contents of "application" XML tag:
     *
     * @throws ConfigurationException If xml content has failed validation.
     */
    protected function setApplicationInfo(): void
    {
        $xml = $this->getTag("application");
        if (empty($xml)) {
            throw new ConfigurationException("Tag is mandatory: application");
        }
        
        $this->defaultFormat = (string) $xml["default_format"];
        if (!$this->defaultFormat) {
            throw new ConfigurationException("Attribute 'default_format' is mandatory for 'application' tag");
        }
        
        $this->defaultRoute = (string) $xml["default_route"];
        if (!$this->defaultRoute) {
            throw new ConfigurationException("Attribute 'default_route' is mandatory for 'application' tag");
        }
        
        $this->viewsPath = (string) $xml->paths["views"];
        $this->version = (string) $xml["version"];
    }
    
    /**
     * Gets default response display format
     *
     * @return string
     */
    public function getDefaultFormat(): string
    {
        return $this->defaultFormat;
    }
    
    /**
     * Gets default route id
     *
     * @return string
     */
    public function getDefaultRoute(): string
    {
        return $this->defaultRoute;
    }
    
    /**
     * Gets path to views folder.
     *
     * @return string
     */
    public function getViewsPath(): string
    {
        return $this->viewsPath;
    }
    
    /**
     * Gets application version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
    
    /**
     * Sets view resolvers info based on contents of "resolvers" XML tag
     *
     * @throws ConfigurationException If xml content has failed validation.
     */
    protected function setResolvers(): void
    {
        $xml = $this->getTag("resolvers");
        if ($xml===null) {
            throw new ConfigurationException("Tag is required: resolvers");
        }
        $list = $xml->xpath("//resolver");
        foreach ($list as $info) {
            $name = (string) $info["format"];
            if (!$name) {
                throw new ConfigurationException("Attribute 'format' is mandatory for 'resolver' tag");
            }
            $this->formats[$name] = new Format($info);
        }
        if (empty($this->formats)) {
            throw new ConfigurationException("Tag is empty: resolvers");
        }
    }
    
    /**
     * Gets content of tag resolver encapsulated as Format objects
     *
     * @param string $displayFormat
     * @return Format|array|null
     */
    public function resolvers(string $displayFormat="")
    {
        if (!$displayFormat) {
            return $this->formats;
        } else {
            return (isset($this->formats[$displayFormat])?$this->formats[$displayFormat]:null);
        }
    }
    
    /**
     * Sets routes info based on contents of "routes" XML tag
     *
     * @throws ConfigurationException If xml content has failed validation.
     */
    protected function setRoutes(): void
    {
        $xml = $this->getTag("routes");
        $list = $xml->xpath("//route");
        foreach ($list as $info) {
            $id = (string) $info['id'];
            if (!$id) {
                throw new ConfigurationException("Route missing 'id' attribute!");
            }
            $this->routes[$id] = new Route($info);
        }
    }
    
    /**
     * Reads content of tag routes encapsulated as Route objects
     *
     * @param string $id
     * @return Route|array|null
     */
    public function routes(string $id="")
    {
        if (!$id) {
            return $this->routes;
        } else {
            return (isset($this->routes[$id])?$this->routes[$id]:null);
        }
    }
    
    /**
     * Gets tag based on name from main XML root or referenced XML file if "ref" attribute was set
     *
     * @param string $name
     * @return \SimpleXMLElement
     * @throws ConfigurationException If XML is misconfigured.
     */
    public function getTag(string $name): \SimpleXMLElement
    {
        $xml = $this->simpleXMLElement->{$name};
        $xmlFilePath = (string) $xml["ref"];
        if ($xmlFilePath) {
            if (isset($this->objectsCache[$name])) {
                return $this->objectsCache[$name];
            } else {
                $xmlFilePath = $xmlFilePath.".xml";
                if (!file_exists($xmlFilePath)) {
                    throw new ConfigurationException("XML file not found: ".$xmlFilePath);
                }
                $subXML = simplexml_load_file($xmlFilePath);
                $returningXML = $subXML->{$name};
                $this->objectsCache[$name] = $returningXML;
                return $returningXML;
            }
        } else {
            return $xml;
        }
    }

    /**
     * Gets root XML tag
     *
     * @return \SimpleXMLElement
     */
    public function getXML(): \SimpleXMLElement
    {
        return $this->simpleXMLElement;
    }
}
