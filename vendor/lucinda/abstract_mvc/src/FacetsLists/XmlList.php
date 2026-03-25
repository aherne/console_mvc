<?php
namespace Lucinda\MVC\FacetsLists;

use Lucinda\MVC\XmlReader\Element;

interface XmlList
{
    function convert(Element $element): array;
}
