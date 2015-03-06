<?php
namespace Rost\Xml;

use SimpleXMLElement;

/**
* This is an experimental replacement for SimpleXmlElement-based workflow.
* It wraps SimpleXmlElement into XMLElement objects. This allows to skip
* a lot of isset() checks and type casts. It works good if you parse XML once
* and store the result for ongoing access.
* 
* SimpleXmlElement example:
* 
* $element = new SimpleXMLElement(...);
* if(isset($element->property->property->property))
* {
*     echo (string)$element->property->property->property;
* }
* 
* XmlParser example:
* 
* $parser = new XmlParser();
* $element = $parser->Parse(...);
* echo $element->property->property->property->GetValue();
*/
class XmlParser
{
	/**
	* @var int LibXml options that should be use during parsing.
	*/
	protected $libXmlOptions;
	
	/**
	* Constructs a new object. Optionally sets LibXml options that should be
	* used during parsing. Uses LIBXML_NOWARNING | LIBXML_NOERROR as the default
	* if no options given.
	* 
	* @param int $libXmlOptions
	* @see http://php.net/manual/libxml.constants.php
	*/
	function __construct($libXmlOptions = null)
	{
		$this->libXmlOptions = $libXmlOptions ?: LIBXML_NOWARNING | LIBXML_NOERROR;
	}
	/**
	* Parses the XML string and returns XmlElement object with the root element.
	* Optionally throws exceptions during the parsing on failure.
	* 
	* @param string $xmlString
	* @param bool $throwExceptions
	* @return XmlElement
	* @throws \Exception if the XML string could not be parsed.
	*/
	function Parse($xmlString, $throwExceptions = false)
	{
		try
		{
			$simpleXmlElement = new SimpleXMLElement($xmlString, $this->libXmlOptions);
			return new XmlElement($simpleXmlElement);
		}
		catch(\Exception $exception)
		{
			if($throwExceptions)
			{
				throw $exception;
			}
		}
		return new XmlElement();
	}
}
