<?php
namespace Rost\Configuration;

/**
* A simple configuration container. Provides a path based
* interface to the contained data.
*/
class Configuration
{
	/**
	* @var mixed[]
	*/
	protected $data;

	/**
	* Constructs a new object and optionally initializes it
	* by the array with configuration data.
	*
	* @param mixed[] $data
	*/
	function __construct(array $data = [])
	{
		$this->data = $data;
	}

	/**
	* Returns an element value by the name or the default value
	* if there is no element set.
	*     
	* @param string $name
	* @param mixed $default
	* @return mixed
	*/
	function Get($name, $default = null)
	{
		return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
	}
	
	/**
	* Returns an element by the slash-separated path or
	* the default value if there is no element set.
	*     
	* @param string $path
	* @param mixed $default
	* @return mixed
	*/
	function GetByPath($path, $default = null)
	{
		$keys = explode('/', $path);
		
		$value = $this->data;
		foreach($keys as $key)
		{
			if(array_key_exists($key, $value))
			{
				$value = $value[$key];
			}
			else
			{
				return $default;
			}
		}
		return $value;	
	}
	
	/**
	* Merges another array into the existing configuration data.
	*
	* For duplicate keys, the following will be performed:
	* - Nested arrays will be merged recursively.
	* - Elements with integer keys will be appended.
	* - Elements with string keys will overwrite current values.
	*
	* @param mixed[] $data
	*/
	function Merge(array $data)
	{
		$this->data = array_replace_recursive($this->data, $data);
	}

	/**
	* Returns the contained configuration as an associative array.
	*
	* @return mixed[]
	*/
	function ToArray()
	{
		return $this->data;
	}
}
