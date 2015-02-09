<?php
namespace Rost\Http;

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
	* Constructs a new object, accepts an array of parameters to wrap.
	* 	
	* @param mixed[] $parameters
	*/
	function __construct(array $parameters)
	{
		$this->parameters = $parameters;
	}
	
	/**
	* Returns true if a parameter with the specified name exists.
	* 
	* @param string $name
	* @return bool
	*/
	function Has($name)
	{
		return array_key_exists($name, $this->parameters);
	}
	
	/**
	* Returns a parameter value by its name, returns the default value
	* if a parameter with the specified name does not exists.
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