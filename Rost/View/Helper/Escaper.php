<?php
namespace Rost\View\Helper;

use Rost\View\Helper\AbstractHelper;
use Rost\Escaper\Escaper as ContextAwareEscaper;

class Escaper extends AbstractHelper
{
	/**
	* @var ContextAwareEscaper
	*/
	protected $escaper;

	/**
	* Constructs the object.
	*/
	protected function __construct()
	{
		$this->escaper = new ContextAwareEscaper(static::$templateManager->GetEncoding());
	}

	/**
	* Escape a string for the HTML context.
	*
	* @param string $value
	* @return string
	*/
	static function Html($value)
	{
		return static::GetInstance()->escaper->EscapeHtml($value);
	}
	
	/**
	* Escape a string for the HTML attribute context.
	*
	* @param string $value
	* @return string
	*/
	static function HtmlAttr($value)
	{
		return static::GetInstance()->escaper->EscapeHtmlAttribute($value);
	}

	/**
	* Escape a string for the Javascript context.
	* 
	* @param string $value
	* @return string
	*/
	static function Js($value)
	{
		return static::GetInstance()->escaper->EscapeJavascript($value);
	}
	
	/**
	* Escapes a string for the URL context.
	* 
	* @param string $value
	* @return string
	*/
	static function Url($value)
	{
		return static::GetInstance()->escaper->EscapeUrl($value);
	}
	
	/**
	* Escapes a string for the CSS context.
	* 
	* @param string $value
	* @return string
	*/
	static function Css($value)
	{
		return static::GetInstance()->escaper->EscapeCss($value);
	}
}
