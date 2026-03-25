<?php

namespace Lucinda\MVC;

/**
 * Implements a list of Facet (injectable) objects
 */
final class FacetCollection
{
    private array $facets = [];

    /**
     * Adds facet to collection
     * 
     * @param string $key Unique identifier
     */
    public function add(string $key, Facet $facet): void {
        if (isset($this->facets[$key])) {
            throw new FacetException("Facet already set for key: ".$key);
        }
        $this->facets[$key] = $facet;
    }

    /**
     * Gets all facets in collection
     * 
     * @return array<string,Facet>
     */
    public function getAll(): array {
        if (empty($this->facets)) {
            throw new FacetException("Facets collection cannot be empty!");
        }
        return $this->facets;
    }
}
