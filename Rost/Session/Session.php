<?php
namespace Rost\Session;

/**
* Session is a wrapper around the standard PHP sessions.
*/
class Session
{
	/**
	* @var bool
	*/
	protected $started = false;
	
	/**
	* Constructs a new object.
	*
	* @param string[] $options An array of session ini directives.
	* @see http://php.net/session.configuration for options
	*/
	function __construct($options = array())
	{
		$this->SetOptions($options);
	}

	/**
	* Sets session.* ini variables.
	*
	* For convenience we omit 'session.' from the beginning of the keys.
	* Explicitly ignores other ini keys.
	*
	* @param string[] $options An array of ini directives.
	* @see http://php.net/session.configuration
	*/
	protected function SetOptions(array $options)
	{
		$validOptions = array_flip(array(
			'cache_limiter', 'cookie_domain', 'cookie_httponly',
			'cookie_lifetime', 'cookie_path', 'cookie_secure',
			'entropy_file', 'entropy_length', 'gc_divisor',
			'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
			'hash_function', 'name', 'referer_check',
			'serialize_handler', 'use_cookies',
			'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
			'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
			'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',
		));

		foreach($options as $key => $value)
		{
			if(isset($validOptions[$key]))
			{
				ini_set('session.' . $key, $value);
			}
		}
	}

	/**
	* Starts the session.
	*
	* @throws \RuntimeException If the session fails to start.
	*/
	function Start()
	{
		if($this->started)
		{
			return;
		}
		if(isset($_SESSION) && session_id())
		{
			throw new \RuntimeException('Failed to start the session: already started by PHP ($_SESSION is set).');
		}
		if(ini_get('session.use_cookies') && headers_sent($file, $line))
		{
			throw new \RuntimeException(sprintf(
				'Failed to start the session because headers have already been sent by "%s" at line %d.',
				$file, $line
			));
		}
		if(!session_start())
		{
			throw new \RuntimeException('Failed to start the session.');
		}
		$this->started = true;
	}

	/**
	* Retruns true if a value with the given name exists.
	*
	* @param string $name
	* @return bool
	*/
	function Has($name)
	{
		return array_key_exists($name, $_SESSION);
	}

	/**
	* Returns a value by the given name if it exists,
	* returns the default value otherwise.
	*
	* @param string $name
	* @param mixed $default
	* @return mixed
	*/
	function Get($name, $default = null)
	{
		if(array_key_exists($name, $_SESSION))
		{
			return $_SESSION[$name];
		}
		return $default;
	}

	/**
	* Sets a value with the given name.
	*
	* @param string $name
	* @param mixed $value
	*/
	function Set($name, $value)
	{
		$_SESSION[$name] = $value;
	}

	/**
	* Removes a value with the given name.
	*
	* @param string $name
	*/
	function Remove($name)
	{
		unset($_SESSION[$name]);
	}

	/**
	* Clears all named values from the session.
	*/
	function Clear()
	{
		$_SESSION = array();
	}

	/**
	* Returns true if the session is started.
	* 
	* @return bool
	*/
	function IsStarted()
	{
		return $this->started;
	}

	/**
	* Force the session to be saved and closed.
	*
	* This method is generally not required for real sessions as
	* the session will be automatically saved at the end of
	* code execution.
	*/
	function Stop()
	{
		session_write_close();
		$this->started = false;
	}
	
	/**
	* Returns the session name, which is used in cookies and URLs.
	* 
	* @return string
	*/
	function GetName()
	{
		return session_name();
	}

	/**
	* Sets the session name, which should be used in cookies and URLs.
	* It should contain only alphanumeric characters and set before the session start.
	* 
	* @param string $name
	*/
	function SetName($name)
	{
		session_name($name);
	}

	/**
	* Returns the session id for the current session or the empty string
	* if there is no current session (no current session id exists). 
	* 
	* @return string
	*/
	function GetId()
	{
		return session_id();
	}

	/**
	* Sets (replaces) the current session id.
	* The method needs to be called before the session start.
	* 
	* @param string $id
	*/
	function SetId($id)
	{
		session_id($id);
	}
}
