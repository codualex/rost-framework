<?php
namespace Rost\View\Helper;

use Rost\View\Helper\AbstractHelper;

/**
* This helper contains methods to create named content sections
* and then use them in arbitrary locations. 
*/
class Section extends AbstractHelper
{
	/**
	* @var string[]
	*/
	protected static $openSectionNames = [];

	/**
	* @var string[]
	*/
	protected static $sections = [];

	/**
	* Opens a named section of the content. Output will be
	* captured until End() method is called.
	*
	* @param string $name
	* @throws \InvalidArgumentException if a section has been opened already.
	*/
	static function Begin($name)
	{
		if(in_array($name, static::$openSectionNames))
		{
			throw new \InvalidArgumentException(sprintf(
				'A section named "%s" has been started already.',
				$name
			));
		}
		static::$openSectionNames[] = $name;
		ob_start();
		ob_implicit_flush(false);
	}

	/**
	* Closes the current content section.
	*
	* @throws \LogicException if no section has been opened yet.
	*/
	static function End()
	{
		if(!static::$openSectionNames)
		{
			throw new \LogicException('Could not close a section. There is no opened section yet.');
		}
		$name = array_pop(static::$openSectionNames);
		static::$sections[$name] = ob_get_clean();
	}

	/**
	* Returns a section content by the given name.
	*
	* @param string $name
	* @return string
	*/
	static function Get($name)
	{
		if(isset(static::$sections[$name]))
		{
			return static::$sections[$name];
		}
		throw new \LogicException('There is no section with the requested name.');
	}
}

