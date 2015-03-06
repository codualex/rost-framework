<?php
namespace Rost\Xml;

use SimpleXMLElement;
use IteratorAggregate;
use EmptyIterator;

/**
* XMLElement wraps SimpleXmlElement and provides a more convenient interface to it.
* Especially this allows to use the fluent interface without checking properties for existence.
* 
* @todo Add methods to get children and attributes as collections.
*/
class XmlElement implements IteratorAggregate
{
	/**
	* @var SimpleXMLElement
	*/
	protected $wrappedElement;
	
	/**
	* Constructs a new object by wrapping the given SimpleXMLElement.
	* 
	* @param SimpleXMLElement $element
	*/
	function __construct(SimpleXMLElement $element = null)
	{
		$this->wrappedElement = $element;
	}
	
	/**
	* Returns an element value or an empty string if it is absent.
	* 
	* @return string
	*/
	function GetValue()
	{
		return (string)$this->wrappedElement;
	}
	
	/**
	* Returns an attribute value by the name or
	* an empty string if the attribute is absent.
	* 
	* @param string $name
	* @return string
	*/
	function GetAttribute($name)
	{
		if($this->wrappedElement)
		{
			return (string)$this->wrappedElement->attributes()->$name;
		}
		return ''; 
	}
	
	/**
	* Returns an iterator for the wrapped element.
	* A part of IteratorAggregate interface.
	* 
	* @return Iterator
	*/
	function getIterator()
	{
		if($this->wrappedElement)
		{
			return new XmlElementIterator($this->wrappedElement);
		}
		return new EmptyIterator();
	}
	
	/**
	* Overloads property access to add the fluent interface support. 
	* 
	* @param string $name
	* @return XmlElement
	*/
	function __get($name)
	{
		if(isset($this->wrappedElement->$name))
		{
			return new static($this->wrappedElement->$name);
		}
		return new static();
	}
	
	/**
	* Returns true if the wrapped element contains property with the given name.
	* 
	* @param string $name
	* @return bool
	*/
	function __isset($name)
	{
		return isset($this->wrappedElement->$name);
	}
}
