<?php
namespace Lucinda\MVC\XmlReader;

final class Element
{
    private string $name;
    private array $attributes=[];
    private array $children=[];

    public function __construct(\SimpleXMLElement $node)
    {
        $this->setName($node);
        $this->setAttributes($node);
        $this->setChildren($node);
    }
    
    private function setName(\SimpleXMLElement $node): void
    {
        $this->name = $node->getName();
    }
    
    public function getName(): string
    {
        return $this->name;
    }

    private function setAttributes(\SimpleXMLElement $node): void
    {
        foreach ($node->attributes() as $k => $v) {
            $this->attributes[(string) $k] = (string) $v;
        }
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    private function setChildren(\SimpleXMLElement $node): void
    {
        foreach ($node->children() as $child) {
            $this->children[$child->getName()][] = new Element($child);
        }
    }
    
    public function getChildren(): array
    {
        return $this->children;
    }
}