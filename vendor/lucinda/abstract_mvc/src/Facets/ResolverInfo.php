<?php

namespace Lucinda\MVC\Facets;

use Lucinda\MVC\Response\Resolver;
use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Encapsulates file format information:
 * - format: file format / name
 * - view resolver: (optional) view resolver class name. If not set, framework will resolve into an empty view with headers only.
    * - content type: content type that corresponds to above file format
    * - character encoding: charset associated to content type
 */
class ResolverInfo
{
    protected string $format;
    protected string $viewResolverClass;
    
    
    public function __construct(Element $element)
    {
        $attributes = $element->getAttributes();
        $this->setFormat($attributes);
        $this->setViewResolver($attributes);
    }
    
    /**
     * Sets content format
     * 
     * @param array<string,string> $attributes
     * @throws Exception If XML is misconfigured.
     */
    protected function setFormat(array $attributes): void
    {
        if (empty($attributes["format"])) {
            throw new Exception("Attribute 'format' is mandatory for 'resolver' tag");
        }
        $this->format = $attributes["format"];
    }
    
    /**
     * Gets content format
     *
     * @return  string
     * @example json
     */
    public function getFormat(): string
    {
        return $this->format;
    }
    
    /**
     * Sets content format
     * 
     * @param array<string,string> $attributes
     * @throws Exception If XML is misconfigured.
     */
    protected function setViewResolver(array $attributes): void
    {
        if (empty($attributes["class"])) {
            throw new Exception("Attribute 'class' is mandatory for 'resolver' tag");
        }
        if (!is_subclass_of($attributes["class"], Resolver::class)) {
            throw new Exception($attributes["class"]." must be child of ".Resolver::class);
        }
        $this->viewResolverClass = $attributes["class"];
    }
    
    /**
     * Gets view resolver class name
     *
     * @return  string
     * @example JsonResolver
     */
    public function getViewResolver(): string
    {
        return $this->viewResolverClass;
    }
}
