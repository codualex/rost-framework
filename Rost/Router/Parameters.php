<?php
namespace Rost\Router;

/**
* Immutable container for an array of named parameters.
*/
class Parameters
{
	/**
	* @var mixed[]
	*/
	protected $parameters;

	/**
	* Constructs the object and accpects an array of parameters to wrap.
	* 	
	* @param mixed[] $parameters
	*/
	function __construct(array $parameters)
	{
		$this->parameters = $parameters;
	}
	
	/**
	* Returns true if there is a parameter with the specified name.
	* 
	* @param string $name
	* @return bool
	*/
	function Has($name)
	{
		return array_key_exists($name, $this->parameters);
	}
	
	/**
	* Returns a parameter value by its name.
	* 
	* @param string $name
	* @param mixed $default
	* @return mixed
	*/
	function Get($name, $default = null)
	{
		if(array_key_exists($name, $this->parameters))
		{
			return $this->parameters[$name];
		}
		return $default;
	}
	
	/**
	* Returns an array with all parameters.
	* 
	* @return mixed[]
	*/
	function ToArray()
	{
		return $this->parameters;
	}
}