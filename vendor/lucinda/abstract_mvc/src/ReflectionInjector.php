<?php

namespace Lucinda\MVC;

/**
 * Implements a Facet constructor injector (for Controller, EventListener and Response\ViewResolver instances)
 */
final class ReflectionInjector
{
    private FacetRegistry $facets;
    
    /**
     * Initializes injector with registry
     * 
     * @param FacetRegistry $facets
     */
    public function __construct(FacetRegistry $facets)
    {
        $this->facets = $facets;
    }
    
    /**
     * Creates an instance and hydrates it with facets
     */
    public function create(string $classToInstance): object
    {
        $rc = new \ReflectionClass($classToInstance);
        $ctor = $rc->getConstructor();
        
        if (!$ctor || $ctor->getNumberOfParameters() === 0) {
            return $rc->newInstance();
        }
        
        $args = [];
        foreach ($ctor->getParameters() as $p) {
            $t = $p->getType();
            if (!$t instanceof \ReflectionNamedType || $t->isBuiltin()) {
                throw new ConfigurationException(
                    "Constructor param must be a class/interface type: {$classToInstance}::\$".$p->getName()
                );
            }
            
            $typeName = $t->getName();
            if (!$this->facets->has($typeName)) {
                throw new FacetException(
                    "Facet not available for {$classToInstance}::\$".$p->getName()." ($typeName)"
                );
            }
            
            $args[] = $this->facets->get($typeName);
        }
        
        return $rc->newInstanceArgs($args);
    }
}
