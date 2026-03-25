<?php

namespace Lucinda\MVC;

/**
 * Registry of facets handled by the MVC framework
 */
final class FacetRegistry
{
    /** @var array<class-string, object> */
    private array $facets = [];
    
    /**
     * Puts facet in collection
     * 
     * @param Facet $facet
     * @return void
     */
    public function put(Facet $facet): void
    {
        if (isset($this->facets[$facet::class])) {
            throw new FacetException("Facet can only be injected once: ".$facet::class);
        }
        $this->facets[$facet::class] = $facet;
    }
    
    /**
     * Puts facet in collection by id
     * 
     * @param string $id
     * @param Facet $facet
     * @return void
     * @throws FacetException If facet was already injected
     */
    public function putAs(string $id, Facet $facet): void
    {
        if (isset($this->facets[$id])) {
            throw new FacetException("Facet can only be injected once: ".$id);
        }
        $this->facets[$id] = $facet; // for interface aliases
    }
    
    /**
     * Attempts to get facet by its unique identifier (class name or id)
     * 
     * @param string $type
     * @return Facet
     */
    public function get(string $type): Facet
    {
        return $this->facets[$type];
    }
    
    /**
     * Checks if facet already exists in collection
     * 
     * @return bool
     */
    public function has(string $type): bool
    {
        return isset($this->facets[$type]);
    }
}