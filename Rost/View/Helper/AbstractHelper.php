<?php
namespace Rost\View\Helper;

use Rost\View\TemplateManager;
use Rost\Router\Router;

/**
* This is a base class for all view helpers. It provides a singleton
* behaviour and an access to TemplateManager and Router instances.
*/
abstract class AbstractHelper
{
	/**
     * @var TemplateManager
     */
    protected static $templateManager;
    
    /**
     * @var Router
     */
    protected static $router;
    
    /**
    * The constructor is protected because we want to block helper class
    * instantiation in any place except helper static methods.
    */
    protected function __construct()
    {
		
    }

	/**
	* Returns an instance of this class. It follows the singleton pattern
	* and always returns the same instance.
	* 
	* @return object
	*/
	protected static function GetInstance()
	{
		static $instance = null;
		if(is_null($instance))
		{
			$instance = new static;
		}
		return $instance;
	}
}
