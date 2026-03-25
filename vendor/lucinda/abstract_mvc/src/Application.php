<?php

namespace Lucinda\MVC;

use Lucinda\MVC\XmlReader\Exception as XmlException;
use Lucinda\MVC\FacetsLists\ResolversList;
use Lucinda\MVC\FacetsLists\RoutesList;
use Lucinda\MVC\Facets\ApplicationInfo;
use Lucinda\MVC\Facets\ResolverInfo;
use Lucinda\MVC\Facets\RouteInfo;

/**
 * Detects settings necessary to configure MVC API based on contents of XML file
 */
abstract class Application
{
    protected XmlReader $reader;
    protected ApplicationInfo $applicationInfo;
    
    /**
     * @var array<string,RouteInfo>
     */
    protected array $routes=array();
    /**
     * @var array<string,ResolverInfo>
     */
    protected array $formats=array();
    
    /**
     * Constructs object
     * 
     * @param string $xmlFilePath File path to the root XML file
     */
    public function __construct(string $xmlFilePath)
    {
        $this->reader = new XmlReader($xmlFilePath);
        $this->setApplicationInfo();
        $this->setResolvers();
        $this->setRoutes();
        
    }
    
    /**
     * Sets information about application based on contents of "application" XML tag
     * 
     * @throws XmlException If xml content has failed validation.
     */
    protected function setApplicationInfo(): void
    {
        $this->applicationInfo = new ApplicationInfo($this->reader->getTag("application"));
    }
    
    /**
     * Gets information about application
     */
    public function getApplicationInfo(): ApplicationInfo
    {
        return $this->applicationInfo;
    }
    

    /**
     * Sets view resolvers info based on contents of "resolvers" XML tag
     *
     * @throws XmlException If xml content has failed validation.
     */
    protected function setResolvers(): void
    {
        $list = new ResolversList(ResolverInfo::class);
        $this->formats = $list->convert($this->reader->getTag("resolvers"));
    }

    /**
     * Gets content of tag resolver encapsulated as Format objects
     *
     * @param string $format
     * @return ResolverInfo|NULL
     */
    public function getResolvers(string $format): ?ResolverInfo
    {
        return $this->formats[$format]??null;
    }

    /**
     * Sets routes info based on contents of "routes" XML tag
     *
     * @throws XmlException If xml content has failed validation.
     */
    protected function setRoutes(): void
    {
        $list = new RoutesList(RouteInfo::class);
        $this->routes = $list->convert($this->reader->getTag("routes"));
    }

    /**
     * Reads content of tag routes encapsulated as Route objects
     *
     * @param string $id
     * @return RouteInfo|NULL
     */
    public function getRoutes(string $id): ?RouteInfo
    {
        return $this->routes[$id]??null;
    }
}
