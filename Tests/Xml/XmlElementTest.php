<?php
namespace Tests\Xml;

use Rost\Xml\XmlParser;
use Rost\Xml\XmlElement;

class XmlElementTest extends \PHPUnit_Framework_TestCase
{
	public function testChainedAccess()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root><node><subnode>content</subnode></node></root>');
		
		$subelement = $element->node->subnode;
		$this->assertInstanceOf(XmlElement::CLASS, $subelement);
		
		$subelement = $element->unknown->unknown;
		$this->assertInstanceOf(XmlElement::CLASS, $subelement);
	}

	public function testIterator()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root><node>content1</node><node>content2</node></root>');
		$count = iterator_count($element->node);
		$this->assertEquals(2, $count);
		$this->assertContainsOnlyInstancesOf(XmlElement::CLASS, $element->node);
	}
	
	public function testEmptyIterator()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root></root>');
		$count = iterator_count($element->unknown);
		$this->assertEquals(0, $count);
	}
	
	public function testGetValue()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root><node>content</node></root>');
		$value = $element->node->GetValue();
		$this->assertEquals('content', $value);
	}
	
	public function testGetMissingValue()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root></root>');
		$value = $element->unknown->GetValue();
		$this->assertSame('', $value);
	}
	
	public function testGetAttribute()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root><node attribute="value"></node></root>');
		$value = $element->node->GetAttribute('attribute');
		$this->assertEquals('value', $value);
	}
	
	public function testGetMissingAttribute()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root></root>');
		$value = $element->unknown->GetAttribute('unknown');
		$this->assertSame('', $value);
	}
	
	public function testIsSet()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root><node><subnode>content</subnode></node></root>');
		$exists = isset($element->node->subnode);
		$this->assertTrue($exists, 'Got false from isset() on existent elements.');
	}
	
	public function testIsNotSet()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root></root>');
		$exists = isset($element->unknown->unknown);
		$this->assertFalse($exists, 'Got true from isset() on nonexistent elements.');
	}
}
