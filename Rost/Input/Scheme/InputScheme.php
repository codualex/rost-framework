<?php
namespace Rost\Input\Scheme;

/**
* The InputScheme object respresents specification for data coming from a user into an application.
* It contains a list of named value schemes, which represent specifications for values that
* the application expects to see in the input data set.
* 
* Some of value schemes in the list are considered active while others are deactivated.
* This allows to work with a subset of the input data in a convenient way.
*/
class InputScheme
{
	/**
	* @var ValueScheme[]
	*/
	protected $valueSchemes = [];
	
	/**
	* @var ValueScheme[]
	*/
	protected $activeValueSchemes = [];
	
	/**
	* Creates and registers a new value scheme with the given name.
	* Returns ValueScheme instance to allow method chaining.
	* 	
	* @param string $name
	* @return ValueScheme
	* @throws \InvalidArgumentException If the given name is already in use.
	*/
	function CreateValueScheme($name)
	{
		if(isset($this->valueSchemes[$name]))
		{
			throw new \InvalidArgumentException(sprintf(
				'A value scheme named "%s" has been created already.',
				$name
			));
		}
		return $this->activeValueSchemes[$name] = $this->valueSchemes[$name] = new ValueScheme($name);
	}
	
	/**
	* Returns a value scheme by the name.
	* 
	* @param string $name
	* @return ValueScheme
	* @throws \InvalidArgumentException If the given name is unknown.
	*/
	function GetValueScheme($name)
	{
		if(!isset($this->valueSchemes[$name]))
        {
			throw new \InvalidArgumentException(sprintf(
				'A value scheme named "%s" does not exist.',
				$name
    		));
		}
		return $this->valueSchemes[$name];
	}
	
	/**
	* Returns an array of all value schemes indexed by names.
	* 
	* @return ValueScheme[]
	*/
	function GetValueSchemes()
	{
		return $this->valueSchemes;
	}
	
	/**
	* Returns an active value scheme by the name.
	* 
	* @param string $name
	* @return ValueScheme
	* @throws \InvalidArgumentException If the requested value scheme is not active at the moment.
	*/
	function GetActiveValueScheme($name)
	{
		if(!isset($this->activeValueSchemes[$name]))
        {
			throw new \InvalidArgumentException(sprintf(
				'A value scheme named "%s" is not active at the moment.',
				$name
    		));
		}
		return $this->activeValueSchemes[$name];
	}
	
	/**
	* Returns an array of all active value schemes indexed by names.
	* 
	* @return ValueScheme[]
	*/
	function GetActiveValueSchemes()
	{
		return $this->activeValueSchemes;
	}
	
	/**
	* Activates the value schemes with the specified names.
	* 
	* @param string[] $names
	*/
	function ActivateValueSchemes(array $names)
	{
		$this->activeValueSchemes = [];
		foreach($names as $name)
		{
			$this->activeValueSchemes[$name] = $this->GetValueScheme($name);
		}
	}
	
	/**
	* Activates all currently existing value schemes.
	*/
	function ActivateAllValueSchemes()
	{
		$this->activeValueSchemes = $this->valueSchemes;
	}
}
