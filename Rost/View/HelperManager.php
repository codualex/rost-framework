<?php
namespace Rost\View;

use Rost\View\Helper\AbstractHelper;
use Rost\View\TemplateManager;
use Rost\Router\Router;

/**
* This class should be used prior to using any view helpers.
* It does initialization by providing TemplateManager and Router to all helpers.
* 
* The class extends AbstractHelper in order to get access to its protected properties,
* but it is not a helper itself and should not be used in templates. 
*/
class HelperManager extends AbstractHelper
{
	/**
	* Sets the TemplateManager instance, it then 
	* will be accessible by all view helpers.
	*
	* @param TemplateManager $templateManager
	*/
	static function SetTemplateManager($templateManager)
	{
		static::$templateManager = $templateManager;
	}
	
	/**
	* Sets the Router instance, it then 
	* will be accessible by all view helpers.
	*
	* @param Router $router
	*/
	static function SetRouter($router)
	{
		static::$router = $router;
	}
}
