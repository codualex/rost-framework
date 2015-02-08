<?php
namespace Rost\Loader;

/**
* PSR-4 compliant class autoloader. Class lookups are performed on
* the filesystem based on the registered namespaces.
* 
* @see http://www.php-fig.org/psr/psr-4/
*/
class ClassLoader
{
	const NAMESPACE_SEPARATOR = '\\';

	/**
	* @var string[] Namespace/directory pairs.
	*/
	protected $namespaces = [];
	
	/**
	* Register a namespace/directory pair.
	*
	* @param string $namespace
	* @param string $directory
	*/
	function RegisterNamespace($namespace, $directory)
	{
		$namespace = rtrim($namespace, self::NAMESPACE_SEPARATOR). self::NAMESPACE_SEPARATOR;
		$this->namespaces[$namespace] = $this->NormalizeDirectory($directory);
	}
	
	/**
	* Normalize the directory to include a trailing directory separator.
	*
	* @param string $directory
	* @return string
	*/
	protected function NormalizeDirectory($directory)
	{
		return rtrim($directory, '/\\') . DIRECTORY_SEPARATOR;
	}
	
	/**
	* Register the autoloader.
	*/
	function Register()
	{
		spl_autoload_register([$this, 'Load']);
	}

	/**
	* Loads (includes) a PHP file based on a class name.
	* This is a callback method for spl_autoload_register function.
	*
	* @param string $class
	* @return bool
	*/
	protected function Load($class)
	{
		foreach($this->namespaces as $namespace => $directory)
		{
			if(strpos($class, $namespace) === 0)
			{
				$truncatedClass = substr($class, strlen($namespace));
				$truncatedClass = str_replace(self::NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $truncatedClass);
				$filename = $directory . $truncatedClass . '.php';
				
				if(file_exists($filename))
				{
					return require $filename;
				}
				return false;
			}
		}
		return false;
	}
}
