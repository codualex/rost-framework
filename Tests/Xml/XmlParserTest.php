<?php
namespace Tests\Xml;

use Rost\Xml\XmlParser;
use Rost\Xml\XmlElement;

class XmlParserTest extends \PHPUnit_Framework_TestCase
{
	public function testParseOnCorrectInput()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root></root>');
		$this->assertInstanceOf(XmlElement::CLASS, $element);
	}
	
	public function testParseOnIncorrectInput()
	{
		$parser = new XmlParser();
		$element = $parser->Parse('<root');
		$this->assertInstanceOf(XmlElement::CLASS, $element);
	}
}
