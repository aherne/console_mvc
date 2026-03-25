<?php
namespace Lucinda\MVC\FacetsLists;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

abstract class FacetsLists
{
    protected string $facetClass;

    public function __construct(string $facetClass)
    {
        $this->facetClass = $facetClass;
    }
}
