<?php
namespace Rost\View\Helper;

use Rost\View\Helper\AbstractHelper;

/**
* This helper contains methods to generate URLs.
*/
class Url extends AbstractHelper
{
	/**
	* Generates URL by the route name.
	* 
	* @param string $routeName
	* @param string[] $parameters
	* @return string
	*/
	static function Generate($routeName, $parameters = [])
	{
		return static::$router->Assemble($routeName, $parameters);
	}
}

