<?php
namespace Rost\Http;

/**
* A container for HTTP headers.
*/
class Headers
{
	/**
	* @var (string|string[])[]
	*/
	protected $headers = [];

	/**
	* @var string[] Original header names.
	*/
	protected $headerNames = [];
	
	/**
	* Constructs a new object and optionally initializes it
	* by an array of header name/value pairs.
	* Each value can be a single value or an array of values.
	*
	* @param (string|string[])[] $values 
	*/
	function __construct(array $values = [])
	{
		foreach($values as $name => $value)
		{
			$this->Set($name, $value);
		}
	}

	/**
	* Returns true if the HTTP header is defined.
	*
	* @param string $name
	* @return bool
	*/
	function Has($name)
	{
		$uniqueKey = strtr(strtolower($name), '_', '-');
		return array_key_exists($uniqueKey, $this->headers);
	}

	/**
	* Returns a header value by the name.
	*
	* @param string $name
	* @param mixed $default The default value.
	* @return string|string[] The header value if there is just one, an array of values otherwise.
	*/
	function Get($name, $default = null)
	{
		$uniqueKey = strtr(strtolower($name), '_', '-');
		if(array_key_exists($uniqueKey, $this->headers))
		{
			return $this->headers[$uniqueKey];
		}
		return $default;
	}

	/**
	* Sets a header value by the name,
	* replaces the existing value if there is any.
	* The value can be a single value or an array of values.
	*
	* @param string $name
	* @param string|string[] $value
	*/
	function Set($name, $value)
	{
		$uniqueKey = strtr(strtolower($name), '_', '-');
		$this->headerNames[$uniqueKey] = $name;
		$this->headers[$uniqueKey] = $value;
	}

	/**
	* Adds a header value by the name without
	* replacing the existing values.
	* The value can be a single value or an array of values.
	*
	* @param string $name
	* @param string|string[] $value
	* @todo Check if array_merge behavior is what we actually need.
	*/
	function Add($name, $value)
	{
		$uniqueKey = strtr(strtolower($name), '_', '-');
		$this->headerNames[$uniqueKey] = $name;

		if(array_key_exists($uniqueKey, $this->headers))
		{
			$this->headers[$uniqueKey] = array_merge(
				(array)$this->headers[$uniqueKey],
				(array)$value
			);
		}
		else
		{
			$this->headers[$uniqueKey] = $value;
		}
	}
	
	/**
	* Returns all headers as an array.
	*
	* @return (string|string[])[]
	*/
	function ToArray()
	{
		return array_combine($this->headerNames, $this->headers);
	}
}