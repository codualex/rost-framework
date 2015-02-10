<?php
namespace Rost\Router\Route;

use Rost\Router\Parameters;
use Rost\Http\Request;

/**
* An interface that all routes should implement.
*/
interface RouteInterface
{
	/**
	* Matches the given request.
	*
	* @param Request $request
	* @return Parameters|null
	*/
	function Match(Request $request);

	/**
	* Assembles the route into URL.
	*
	* @param string[] $routeParameters
	* @param string[] $queryParameters
	* @return string
	*/
	function Assemble($routeParameters = [], $queryParameters = []);
}
