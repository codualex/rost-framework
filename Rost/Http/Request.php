<?php
namespace Rost\Http;

/**
* Request represents the current HTTP request.
*/
class Request
{
	/**
	* @var Parameters
	*/
	protected $queryParameters;

	/**
	* @var Parameters
	*/
	protected $postParameters;
	
	/**
	* @var Parameters
	*/
	protected $serverParameters;

	/**
	* @var Parameters
	*/
	protected $cookies;
	
	/**
	* @var Parameters
	*/
	protected $files;

	/**
	* @var RequestHeaders
	*/
	protected $headers;
	
	/**
	* @var string
	*/
	protected $method;
		
	/**
	* @var string
	*/
	protected $uri;

	/**
	* @var string
	*/
	protected $relativePath;
	
	/**
	* Constructs a new Request object based on the given environment data.
	*
	* @param array $get Data similar to that which is typically provided by $_GET
	* @param array $post Data similar to that which is typically provided by $_POST
	* @param array $server Data similar to that which is typically provided by $_SERVER
	* @param array $cookies Data similar to that which is typically provided by $_COOKIE
	* @param array $files Data similar to that which is typically provided by $_FILES
	*/
	function __construct(array $get, array $post, array $server, array $cookies, array $files)
	{
		$this->queryParameters = new Parameters($get);
		$this->postParameters = new Parameters($post);
		$this->serverParameters = new Parameters($server);
		$this->cookies = new Parameters($cookies);
		$this->files = new Parameters($this->UntangleUploadedFiles($files));

		$this->headers = RequestHeaders::CreateFromServerParameters($server);
		
		$this->method = $this->serverParameters->Get('REQUEST_METHOD', 'GET');
		$this->uri = $this->serverParameters->Get('REQUEST_URI', '/');
	}
	
	/**
	* Considers the environment information found in PHP superglobals
	* and creates a new instance of the class matching that data.
	*
	* @return static
	*/
	static function CreateFromEnvironment()
	{
		return new static($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
	}

	/**
	* Returns a read-only container with query parameters,
	* which are typically provided by $_GET.
	* 
	* @return Parameters
	*/
	function GetQueryParameters()
	{
		return $this->queryParameters;
	}
	
	/**
	* Returns a read-only container with server parameters,
	* which are typically provided by $_POST.
	* 
	* @return Parameters
	*/
	function GetPostParameters()
	{
		return $this->postParameters;
	}

	/**
	* Returns a read-only container with server parameters,
	* which are typically provided by $_SERVER.
	* 
	* @return Parameters
	*/
	function GetServerParameters()
	{
		return $this->serverParameters;
	}
	
	/**
	* Returns a read-only container with cookies,
	* which are typically provided by $_COOKIE.
	* 
	* @return Parameters
	*/
	function GetCookies()
	{
		return $this->cookies;
	}
	
	/**
	* Returns a read-only container with uploaded files,
	* which are typically provided by $_FILES.
	* 
	* @return Parameters
	*/
	function GetFiles()
	{
		return $this->files;
	}
	
	/**
	* Returns an HTTP method name, typically 'GET' or 'POST'.
	* 
	* @return string
	*/
	function GetMethod()
	{
		return $this->method;
	}

	/**
	* Returns the request path relative to the application root.
	* The path starts with a leading slash.
	*
	* @return string
	*/
	function GetRelativePath()
	{
		if($this->relativePath === null)
		{
			$phpSelf = $this->serverParameters->Get('PHP_SELF');
			$script = $this->serverParameters->Get('SCRIPT_NAME');
		
			$this->relativePath = substr($phpSelf, strlen($script));
			$this->relativePath = '/' . trim($this->relativePath, '/');
		}
		return $this->relativePath;
	}

	/**
	* Transforms the convoluted $_FILES-like array into a manageable form.
	* This handles a situation when an input element name contains brackets.
	* For example: <input type="file" name="myfile[]">
	*
	* @param mixed[] $convolutedFiles
	* @return mixed[]
	*/	
	protected function UntangleUploadedFiles(array $convolutedFiles)
	{
		$files = [];
		foreach($convolutedFiles as $name => $parameters)
		{
			$files[$name] = [];
			foreach($parameters as $parameterName => $parameterValue)
			{
				$files[$name] = $this->UntangleRecursively($files[$name], $parameterName, $parameterValue);
			}
		}
		return $files;
	}

	/**
	* Untangles a single parameter recursively and injects all
	* found values into the given array. Returns the modified array.
	* 
	* @param mixed[] $array
	* @param string $parameterName
	* @param mixed|mixed[] $parameterValues
	* @return mixed[]
	*/
	protected function UntangleRecursively($array, $parameterName, $parameterValues)
	{
		if(is_array($parameterValues))
		{
			foreach($parameterValues as $key => $value)
			{
				isset($array[$key]) or $array[$key] = [];
				$array[$key] = $this->UntangleRecursively($array[$key], $parameterName, $value);
			}
			return $array;
		}
		$array[$parameterName] = $parameterValues;
		return $array;
	}
}