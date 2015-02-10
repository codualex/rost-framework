<?php
namespace Rost\Router\Route;

use Rost\Router\Route\RouteInterface;
use Rost\Router\Parameters;
use Rost\Http\Request;

class LiteralRoute implements RouteInterface
{
	/**
	* @var string A literal path to look for (includes a leading slash).
	*/
	protected $path;
    
	/**
	* @var mixed[]
	*/
    protected $defaultParameters = [];
	
	/**
	* Constructs the route based on the given definition.
	* 
	* @param mixed[] $definition
	* @todo Should we show an exception when parameters is not an array?
	*/
	function __construct($definition)
	{
		if(!isset($definition['path']))
		{
			throw new \InvalidArgumentException(
				'The route definition must contain a "path" key, but it is not there.'
			);
		}
		$this->path = $definition['path'];
		
		if(isset($definition['parameters']))
		{
			$this->defaultParameters = $definition['parameters'];
		}
	}

	/**
	* Matches a given request.
	*
	* @param Request $request
	* @return Parameters|null
	*/
	function Match(Request $request)
	{
		if($this->path == $request->GetRelativePath())
		{
			return new Parameters($this->defaultParameters);
		}
		return null;
	}
	
	/**
	* Assembles the route into URL.
	*
	* @param string[] $routeParameters Not in use for this route type.
	* @param string[] $queryParameters
	* @return string
	*/
	function Assemble($routeParameters = [], $queryParameters = [])
	{
		if($queryParameters)
		{
			return $this->path . '?' . http_build_query($queryParameters);
		}
		return $this->path;
	}
}
