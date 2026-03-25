<?php
namespace Lucinda\MVC;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception as XMLException;

final class XmlReader
{

    protected \SimpleXMLElement $simpleXMLElement;

    /**
     * Reads XML supplied
     *
     * @param  string $xmlFilePath Relative location of XML file containing settings.
     * @throws XMLException If XML is misconfigured.
     */
    public function __construct(string $xmlFilePath)
    {
        if (!file_exists($xmlFilePath)) {
            throw new XMLException("XML file not found: ".$xmlFilePath);
        }
        $simpleXMLElement = simplexml_load_file($xmlFilePath);
        if (!$simpleXMLElement) {
            throw new XMLException("XML is unreadable or malformed: ".$xmlFilePath);
        }
        $this->simpleXMLElement = $simpleXMLElement;
    }


    /**
     * Gets tag based on name from main XML root or referenced XML file if "ref" attribute was set
     *
     * @param  string $name
     * @return Element
     * @throws XMLException If XML is misconfigured.
     */
    public function getTag(string $name): Element
    {
        $xml = $this->simpleXMLElement->{$name};
        if (!$xml) {
            throw new XMLException("Tag not found in XML: ".$name);
        }
        $xmlFilePath = (string) $xml["ref"];
        if (!$xmlFilePath) {
            throw new XMLException("It is mandatory for each root tag to reference a separate file: ".$name);
        }
        $xmlFilePath = $xmlFilePath.".xml";
        if (!file_exists($xmlFilePath)) {
            throw new XMLException("XML file not found: ".$xmlFilePath);
        }
        $subXML = simplexml_load_file($xmlFilePath);
        if (!$subXML) {
            throw new XMLException("XML is unreadable or malformed: ".$xmlFilePath);
        }
        $returningXML = $subXML->{$name};
        if (!$returningXML) {
            throw new XMLException("Root element is missing in ".$xmlFilePath.": ".$name);
        }

        return new Element($returningXML);
    }
    
    
    public function hasTag(string $name): bool
    {
        $xml = $this->simpleXMLElement->{$name};
        return !empty($xml);
    }
}
