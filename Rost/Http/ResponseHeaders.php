<?php
namespace Rost\Http;

/**
* A container for response HTTP headers.
* It also has convenient methods to add cookie headers.
*/
class ResponseHeaders extends Headers
{
	/**
	* Sets a cookie.
	*
	* @param string $name The cookie name as a valid token (RFC 2616).
	* @param string $value The value to store in the cookie.
	* @param int|DateTime $expires Date and time after which this cookie expires.
	* @param string $domain The host to which the user agent will send this cookie.
	* @param string $path The path describing the scope of this cookie.
	* @param bool $secure If this cookie should only be sent through a "secure" channel by the user agent.
	* @param bool $httpOnly If this cookie should only be used through the HTTP protocol.
	*/
	function SetCookie($name, $value = null, $expires = 0, $domain = null, $path = '/', $secure = false, $httpOnly = true)
	{
		$value = $this->CreateCookieHeaderValue($name, $value, $expires, $domain, $path, $secure, $httpOnly);
		$this->Add('Set-Cookie', $value);
	}

	/**
	* Marks the cookie for removal.
	*
	* On executing this method, the expiry time of this cookie is set to a point
	* in time in the past. This triggers the removal of the cookie in the user agent.
	*
	* @param string $name The cookie name as a valid token (RFC 2616).
	*/
	function ExpireCookie($name)
	{
		$value = $this->CreateCookieHeaderValue($name, 'deleted', 1);
		$this->Add('Set-Cookie', $value);
	}	

	/**
	* Returns a string suitable for a HTTP "Set-Cookie" header value.
	* 
	* @param string $name The cookie name as a valid token (RFC 2616).
	* @param string $value The value to store in the cookie.
	* @param int|DateTime $expires Date and time after which this cookie expires.
	* @param string $domain The host to which the user agent will send this cookie.
	* @param string $path The path describing the scope of this cookie.
	* @param bool $secure If this cookie should only be sent through a "secure" channel by the user agent.
	* @param bool $httpOnly If this cookie should only be used through the HTTP protocol.
	* @return string
	*/
	protected function CreateCookieHeaderValue($name, $value = null, $expires = 0, $domain = null, $path = '/', $secure = false, $httpOnly = true)
	{
		if(preg_match("/[=,; \t\r\n\013\014]/", $name))
		{
			throw new \InvalidArgumentException(sprintf(
				'The cookie name "%s" contains invalid characters.',
				$name
			));
		}
		if(strlen($name))
		{
			throw new \InvalidArgumentException('The cookie name cannot be empty.');
		}

		$string = sprintf('%s=%s', $name, urlencode($value));

		if($expires instanceof \Datetime)
		{
			$expires = $expires->getTimestamp();
		}
		if(!is_integer($expires))
		{
			throw new \InvalidArgumentException(
				'The parameter "expires" must be a unix timestamp or a DateTime object.'
			);
		}
		if($expires !== 0)
		{
			$string .= '; Expires=' . gmdate('D, d-M-Y H:i:s T', $expires);
		}
		if($domain !== null)
		{
			$string .= '; Domain=' . $domain;
		}
		$string .= '; Path=' . $path;
		if($secure)
		{
			$string .= '; Secure';
		}
		if($httpOnly)
		{
			$string .= '; HttpOnly';
		}
		return $string;
	}
}