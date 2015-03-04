<?php
namespace Rost\Url;

use Rost\Url\Query;

/**
* This class is a thin wrapper around the standard parse_url function.
* It helps to read and modify URL components in a more convenient way.
* 
* Emits E_WARNING on seriously malformed URLs before PHP 5.3.3.
*/
class Url
{
	protected $url = '';
	
	protected $scheme;
	protected $host;
	protected $port;
	protected $user;
	protected $password;
	protected $path;
	protected $query;
	protected $fragment;
	
	protected $componentUpdated = false;

	/**
	* Creates a new Url object.
	*
	* @param string $url Optional initial URL string.
	*/	
	function __construct($url = null)
	{
		if($url)
		{
			$this->url = $url;
			$this->Parse();
		}
		if(!$this->query)
		{
			$this->query = new Query();
		}
	}

	/**
	* Parses URL into components.
	*/
	protected function Parse()
	{
		$components = parse_url($this->url);
		if($components)
		{
			$this->scheme = isset($components['scheme']) ? $components['scheme'] : '';
			$this->host = isset($components['host']) ? $components['host'] : '';
			$this->port = isset($components['port']) ? $components['port'] : '';
			$this->user = isset($components['user']) ? $components['user'] : '';
			$this->password = isset($components['pass']) ? $components['pass'] : '';
			$this->path = isset($components['path']) ? $components['path'] : '';
			$this->fragment = isset($components['fragment']) ? $components['fragment'] : '';
			
			if(isset($components['query']))
			{
				$this->query = new Query($components['query']);
			}
		}
	}

	/**
	* Updates a URL schema.
	* 
	* @param string $scheme
	*/	
	function SetScheme($scheme)
	{
		$this->scheme = $scheme;
		$this->componentUpdated = true;
	}

	/**
	* Returns a URL schema.
	* 
	* @return string
	*/	
	function GetScheme()
	{
		return $this->scheme;
	}

	/**
	* Updates a host component of the URL.
	* 
	* @param string $host
	*/	
	function SetHost($host)
	{
		$this->host = $host;
		$this->componentUpdated = true;
	}

	/**
	* Returns a host component of the URL.
	* 
	* @return string
	*/	
	function GetHost()
	{
		return $this->host;
	}

	/**
	* Updates a port component of the URL.
	* 
	* @param int $port
	*/	
	function SetPort($port)
	{
		$this->port = $port;
		$this->componentUpdated = true;
	}
	
	/**
	* Returns a port component of the URL.
	* 
	* @return int
	*/	
	function GetPort()
	{
		return $this->port;
	}

	/**
	* Updates a user component of the URL.
	* 
	* @param string $user
	*/		
	function SetUser($user)
	{
		$this->user = $user;
		$this->componentUpdated = true;
	}

	/**
	* Returns a user component of the URL.
	* 
	* @return string
	*/	
	function GetUser()
	{
		return $this->user;
	}

	/**
	* Updates a password component of the URL.
	* 
	* @param string $fragment
	*/	
	function SetPassword($password)
	{
		$this->password = $password;
		$this->componentUpdated = true;
	}
	
	/**
	* Returns a password component of the URL.
	* 
	* @return string
	*/	
	function GetPassword()
	{
		return $this->password;
	}	

	/**
	* Updates a path component of the URL.
	* 
	* @param string $path
	*/	
	function SetPath($path)
	{
		$this->path = $path;
		$this->componentUpdated = true;
	}
	
	/**
	* Returns a path component of the URL.
	* 
	* @return string
	*/	
	function GetPath()
	{
		return $this->path;
	}
	
	/**
	* Replaces the contained Query object.
	* All future changes to the Query affect the Url object.
	* 
	* @param Query $query
	*/
	function SetQuery(Query $query)
	{
		$this->query = $query;
		$this->componentUpdated = true;
	}
	
	/**
	* Returns the contained Query object.
	* All changes to the Query affect the Url object.
	* 
	* @return Query
	*/
	function GetQuery()
	{
		return $this->query;
	}
	
	/**
	* Updates a fragment component of the URL.
	* 
	* @param string $fragment
	*/
	function SetFragment($fragment)
	{
		$this->fragment = $fragment;
		$this->componentUpdated = true;
	}
	
	/**
	* Returns a fragment component of the URL.
	* 
	* @return string
	*/
	function GetFragment()
	{
		return $this->fragment;
	}
	
	/**
	* Returns the URL converted to a string.
	* 
	* @return string
	* @todo Replace strlen functions by logical conditions.
	*/
	function ToString()
	{
		if($this->componentUpdated || $this->query->IsModified())
		{
			$this->url = '';
			
			if(strlen($this->scheme))
			{
				$this->url .= $this->scheme . ':';
			}
			
			if(strlen($this->host))
			{
				$this->url .= '//';
				
				if(strlen($this->user))
				{
					$this->url .= $this->user;
					if(strlen($this->password))
					{
						$this->url .= ':' . $this->password;
					}
					$this->url .= '@';
				}
				$this->url .= $this->host;
				if($this->port)
				{
					$this->url .= ':' . $this->port;
				}
			}
			
			if(strlen($this->path))
			{
				$this->url .= $this->path;
			}
			
			$query = $this->query->ToString();
			if(strlen($query))
			{
				 $this->url .= "?" . $query;
			}
			
			if(strlen($this->fragment))
			{
				$this->url .= "#" . $this->fragment;
			}
			$this->componentUpdated = false;
		}
		return $this->url;
	}

	/**
	* Magic method to convert the URL to a string.
	* 
	* @return string
	*/
	function __toString()
	{
		return $this->ToString();
	}
}