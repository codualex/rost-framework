<?php
namespace Rost\Router;

use Rost\Router\Route\RouteInterface;

/**
* The Builder object works as a factory and constructs routers based on
* definitions. Definitions are configuration-like arrays.
*/
class Builder
{
	/**
	* @var string[] An array of type/class name pairs for the known route types. 
	*/
	protected $routeTypes;

	/**
	* Constructs a new object.
	*/
	function __construct()
	{
		$this->routeTypes = [
			'literal' => 'Rost\Router\Route\LiteralRoute',
			'pattern' => 'Rost\Router\Route\PatternRoute'
		];
	}
	
	/**
	* Registers a new route type.
	* 
	* @param string $type
	* @param string $class
	* @todo Do we need an exception on an attemp to override the type?
	*/
	function RegisterRouteType($type, $class)
	{
		$this->routeTypes[$type] = $class;
	}
	
	/**
	* Creates a router based on the array with route definitions.
	* 
	* @param mixed[] $definitions
	* @return Router
	*/
	function CreateRouter($definitions)
	{
		$router = new Router();
		foreach($definitions as $name => $definition)
		{
			$route = $this->CreateRoute($definition);	
			$router->AddRoute($name, $route);
		}
		return $router;
	}
	
	/**
	* Creates a route based on the definition.
	* 
	* @param mixed[] $definition
	* @return RouteInterface
	* @todo Check a route class for the route interface.
	*/
	function CreateRoute($definition)
	{
		if(!isset($definition['type']))
		{
			throw new \InvalidArgumentException(
				'Each route definition must contain a "type" key, but it is not there.'
			);
		}
		$type = $definition['type'];
		
		if(!isset($this->routeTypes[$type]))
		{
			throw new \InvalidArgumentException(sprintf(
				'Requested an unknown type of a route: "%s".',
				$type
			));
		}
		$class = $this->routeTypes[$type];
		
		if(!class_exists($class, true))
		{
			throw new \InvalidArgumentException(sprintf(
				'A class implementing "%s" route type does not exist: "%s".',
				$type,
				$class
			));
		}
		return new $class($definition);
	}
}
