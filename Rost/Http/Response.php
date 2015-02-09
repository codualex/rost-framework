<?php
namespace Rost\Http;

/**
* Response represents an HTTP response.
*/
class Response
{
	/**
	* @var int HTTP status code.
	*/
	protected $statusCode;

	/**
	* @var string HTTP status message.
	*/
	protected $statusMessage;

	/**
	* @var ResponseHeaders
	*/
	protected $headers;
	
	/**
	* @var string
	*/
	protected $content;

	/**
	* Constructs a new Response object.
	*
	* @param string $content
	* @param int $statusCode
	*/
	function __construct($content = '', $statusCode = Status::OK)
	{
		$this->headers = new ResponseHeaders();
		$this->SetContent($content);
		$this->SetStatus($statusCode);
	}

	/**
	* Sets the HTTP status code and optionally a customized message.
	*
	* @param int $code
	* @param string $message If specified, this message is sent instead of the standard message.
	*/
	function SetStatus($code, $message = null)
	{
		$this->statusCode = $code;
		$this->statusMessage = is_null($message) ? Status::GetMessageByCode($code) : $message;
	}
	
	/**
	* Get Sets the content.
	*
	* @param string $content The body content.
	*/
	function SetContent($content)
	{
		$this->content = $content;
	}

	/**
	* Returns response headers as ResponseHeaders instance.
	*
	* @return ResponseHeaders
	*/
	function GetHeaders()
	{
		return $this->headers;
	}

	/**
	* Sends HTTP headers and the content.
	*/
	function Send()
	{
		$this->SendHeaders();
		if($this->content !== null)
		{
			echo $this->content;
		}
	}

	/**
	* Sends the HTTP headers to the client.
	* The method must be called before any actual output is sent.
	*/
	function SendHeaders()
	{
		header(sprintf('HTTP/1.1 %s %s', $this->statusCode, $this->statusMessage), true, $this->statusCode);

		foreach($this->headers->ToArray() as $name => $value)
		{
			if(is_array($value))
			{
				foreach($value as $textualValue)
				{
					header($name.': '.$textualValue, false);
				}
			}
			else
			{
				header($name.': '.$value, false);
			}
		}
	}
}
