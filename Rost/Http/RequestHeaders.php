<?php
namespace Rost\Http;

/**
* An immutable container for request HTTP headers.
* It also has a convenient method to utilize $_SERVER-like arrays for the initialization.
*/
class RequestHeaders
{
	/**
	* @var Headers
	*/
	protected $headers;

	/**
	* Constructs a new RequestHeaders object and optionally
	* initializes it by an array of header name/value pairs.
	* Each value can be a single value or an array of values.
	*
	* @param (string|string[])[] $values 
	*/
	function __construct(array $values = [])
	{
		$this->headers = new Headers($values);
	}
	
	/**
	* Creates a new RequestHeaders instance from the given $_SERVER-like array.
	*
	* @param string[] $server
	* @return static
	*/
	static function CreateFromServerParameters(array $server)
	{
		$headers = [];
		if(isset($server['PHP_AUTH_USER']) && isset($server['PHP_AUTH_PW']))
		{
			$headers['Authorization'] = 'Basic ' . base64_encode($server['PHP_AUTH_USER'] . ':' . $server['PHP_AUTH_PW']);
		}
		
		foreach($server as $name => $value)
		{
			if(strpos($name, 'HTTP_') === 0)
			{
				$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
				$headers[$name] = $value;
			}
			elseif($name === 'CONTENT_TYPE')
			{
				$headers['Content-Type'] = $value;
			}
			elseif($name === 'CONTENT_LENGTH')
			{
				$headers['Content-Length'] = $value;
			}
		}
		return new static($headers);
	}

	/**
	* Returns true if the HTTP header is defined.
	*
	* @param string $name
	* @return bool
	*/
	function Has($name)
	{
		return $this->headers->Has($name);
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
		return $this->headers->Get($name, $default);
	}
}