<?php

namespace ABCoursework;

/**
 * XmlParser: Parses XML strings into arrays where the keys are the Elements and the values are the data, for attributes
 * the key is ELEMENT.ATTRIBUTE and the value is the attribute value.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class XmlParser
{
    /**
     * @var resource Reference to an XML Parser.
     */
    private $xmlParser = null;

    /**
     * @var array Parsed XML data.
     */
    private array $parsedData = [];

    /**
     * @var string Name of the current XML element.
     */
    private string $elementName = '';

    /**
     * @var array Temporary attributes array, for use with openElement and processElementData.
     */
    private array $tempAttributes = [];

    /**
     * Frees the XmlParser memory once the class is no longer being used.
     */
    public function __destruct()
    {
        if ($this->xmlParser !== null) {
            xml_parser_free($this->xmlParser);
        }
    }

    /**
     * Creates a new XML parser object, freeing the old one if one exists, to clear the data ready for another
     * XML string.
     */
    private function createXmlParser()
    {
        if ($this->xmlParser !== null) {
            xml_parser_free($this->xmlParser);
        }
        $this->xmlParser = xml_parser_create();
        xml_set_object($this->xmlParser, $this);
        xml_set_element_handler($this->xmlParser, "openElement", "closeElement");
        xml_set_character_data_handler($this->xmlParser, "processElementData");
    }

    /**
     * Parses a given XML string into an array, escaping values for HTML display,
     * keys being elements and attributes and the values being the escaped element data or attribute value.
     * @param string $xmlToParse XML String to parse.
     * @return array An associative array of the parsed XML data.
     */
    public function parseXml(string $xmlToParse): array
    {
        $this->createXmlParser();
        $this->parsedData = [];
        $this->tempAttributes = [];
        xml_parse($this->xmlParser, $xmlToParse);
        return $this->parsedData;
    }

    /**
     * A function that runs when an element's opening tag is reached, setting the current element name and populating
     * a temporary attribute array to be used in processElementData().
     * @param $parser resource Parser created in constructor.
     * @param $elementName string Current element's name, given by parser.
     * @param $attributes array|null Associative array of the element's attributes.
     */
    private function openElement($parser, $elementName, $attributes)
    {
        $this->elementName = $elementName;
        if ($attributes !== null)
        {
            foreach ($attributes as $attrName => $attrValue)
            {
                $tagAttrName = $elementName . '.'. $attrName;
                $this->tempAttributes[$tagAttrName] = $attrValue;
            }
        }
    }

    /**
     * If an elements contains data i.e. attributes or a value and hasn't been processed before, store the
     * values and attribute information in an array.
     * @param $parser resource Parser created in constructor.
     * @param $elementData mixed Current element's data.
     */
    private function processElementData($parser, $elementData)
    {
        if (array_key_exists($this->elementName, $this->parsedData) === false)
        {
            $this->parsedData[$this->elementName] = htmlspecialchars(trim($elementData), ENT_COMPAT | ENT_HTML5);
            foreach ($this->tempAttributes as $tagAttrName => $tagAttrValue)
            {
                $this->parsedData[$tagAttrName] = htmlspecialchars(trim($tagAttrValue), ENT_COMPAT | ENT_HTML5);
            }
        }
    }

    /**
     * A function that runs when a closing tag of an element is reached, for the current situation nothing needs
     * to occur here.
     * @param $parser resource Parser created in constructor.
     * @param $elementName string Current element's name, given by parser.
     */
    private function closeElement($parser, $elementName) {} // No action needed for closing elements in our case.

}