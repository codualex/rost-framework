<?php
namespace Rost\Router;

use Rost\Router\Route\RouteInterface;
use Rost\Router\Parameters;
use Rost\Http\Request;

/**
* URI routing is the process of taking the requested URI and deciding which
* application handler will handle the current request.
* 
* The Router mantains a collection of routes and selects one based on the request.
* Each route contains rules in order to match particular request only.
* Based on the matched route an application can decide what to do next.
*/
class Router
{
	/**
	* @var RouteInterface[]
	*/
	protected $routes = [];
	
	/**
	* Adds a named route to the router.
	* 
	* @param string $name
	* @param RouteInterface $route
	*/
	function AddRoute($name, $route)
	{
		$this->routes[$name] = $route;
	}

	/**
	* Matches the given request. Returns a Parameters instance on success,
	* or null if there is not a route matching the request.
	*
	* @param  Request $request
	* @return Parameters|null
	*/
	function Match(Request $request)
	{
		foreach($this->routes as $name => $route)
		{
			$parameterContainer = $route->Match($request);
			if($parameterContainer)
			{
				return $parameterContainer;
			}
		}
		return null;
	}

	/**
	* Assembles the route into URL.
	*
	* @param string $name
	* @param string[] $parameters
	* @return string
	* @throws \InvalidArgumentException If the given route name is unknown.
	*/
	function Assemble($name, $parameters = [])
	{
		if(!isset($this->routes[$name]))
		{
			throw new \InvalidArgumentException(sprintf(
				'Route named "%s" is unknown.',
				$name
			));
		}
		return $this->routes[$name]->Assemble($parameters);
	}
}
