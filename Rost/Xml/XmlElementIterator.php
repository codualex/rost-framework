<?php
namespace Rost\Xml;

use IteratorIterator;
use SimpleXMLElement;

/**
* When SimpleXmlElement works as a collection we use XmlElementIterator
* to convert each element in the collection to XmlElement object. 
*/
class XmlElementIterator extends IteratorIterator
{
	/**
	* Constructs a new object.
	*
	* @param SimpleXMLElement $simpleXmlElement
	*/
	function __construct(SimpleXMLElement $simpleXmlElement)
	{
		parent::__construct($simpleXmlElement);
	}

	/**
	* Returns the current element wrapped in XmlElement.
	* Overloads a method in IteratorIterator.
	* 
	* @return XmlElement
	*/
	function current()
	{
		return new XmlElement(parent::current());
	}
}
