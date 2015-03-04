<?php
namespace Rost\Url;

/**
* This class is a thin wrapper around the standard parse_str and http_build_query functions.
* It helps to read and modify URL query in a more convenient way.
*/
class Query
{
	protected $query = '';
	protected $parameters = array();
	
	protected $parsingRequired = false;
	protected $parameterUpdated = false;
	protected $modified = false;

	/**
	* Creates a new Query object.
	*
	* @param string $query Optional initial query string.
	*/	
	function __construct($query = null)
	{
		if(strlen($query))
		{
			$this->query = $query;
			$this->parsingRequired = true;
		}
	}
	
	/**
	* Parses parameter names and values out of the query.
	*/	
	protected function Parse()
	{
		parse_str($this->query, $this->parameters);
		$this->parsingRequired = false;	
	}

	/**
	* Updates a parameter by a new value.
	* 
	* @param string $name
	* @param string $value
	*/
	function SetParameter($name, $value)
	{
		if($this->parsingRequired)
		{
			$this->Parse();
		}
		$this->parameters[$name] = $value;
		$this->parameterUpdated = true;
		$this->modified = true;
	}

	/**
	* Returns true if the Query contains a parameter with the specified name.
	* 	
	* @param string $name
	* @return bool
	*/	
	function HasParameter($name)
	{
		if($this->parsingRequired)
		{
			$this->Parse();
		}
		return isset($this->parameters[$name]);
	}

	/**
	* Returns a parameter value by name.
	* 	
	* @param string $name
	* @param string $defaultValue Optional default value.
	* @return string
	*/
	function GetParameter($name, $defaultValue = null)
	{
		if($this->parsingRequired)
		{
			$this->Parse();
		}
		return isset($this->parameters[$name]) ? $this->parameters[$name] : $defaultValue;
	}

	/**
	* Removes a parameter by name.
	* 	
	* @param string $name
	*/
	function RemoveParameter($name)
	{
		if($this->parsingRequired)
		{
			$this->Parse();
		}
		if(isset($this->parameters[$name]))
		{
			unset($this->parameters[$name]);
			$this->parameterUpdated = true;
			$this->modified = true;
		}
	}

	/**
	* Returns true is the Query has been modified since the creation.
	* 
	* @return bool
	*/
	function IsModified()
	{
		return $this->modified;
	}

	/**
	* Returns the Query converted to a string.
	* 
	* @return string
	*/	
	function ToString()
	{
		if($this->parameterUpdated)
		{
			$this->query = http_build_query($this->parameters);
			$this->parameterUpdated = false;
		}
		return $this->query;
	}
	
	/**
	* Magic method to convert the Query to a string.
	* 
	* @return string
	*/
	function __toString()
	{
		return $this->ToString();
	}
}