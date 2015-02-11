<?php
namespace Rost\View\Helper;

use Rost\View\Helper\AbstractHelper;

/**
* This helper contains methods to work with templates.
*/
class Template extends AbstractHelper
{
	/**
	* Renders a template and returns it as a string. Optionally accepts
	* extra variables which should be visible inside the template in addition
	* to global template variables which are visible in all templates.
	* 
	* @param string $template
	* @param mixed[] $extraVariables
	* @return string
	*/
	static function Render($template, $extraVariables = [])
	{
		return static::$templateManager->Render($template, $extraVariables);
	}
}

