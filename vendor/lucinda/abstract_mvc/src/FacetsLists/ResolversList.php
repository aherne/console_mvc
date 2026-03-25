<?php
namespace Lucinda\MVC\FacetsLists;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

final class ResolversList extends FacetsLists implements XmlList
{
    public function convert(Element $element): array
    {
        $output = [];
        $list = $element->getChildren();
        if (empty($list["resolver"])) {
            throw new Exception("At least one 'resolver' tag is mandatory for 'resolvers' children");
        }
        $facetClass = $this->facetClass;
        foreach ($list["resolver"] as $info) {
            $info = new $facetClass($element);
            $output[$info->getFormat()] = $info;
        }
        return $output;
    }
}
