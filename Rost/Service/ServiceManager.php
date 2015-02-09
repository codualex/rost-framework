<?php
namespace Rost\Service;

/**
* ServiceManager class implements the Service Locator design pattern.
* It works as a container for service definitions. Each service definition
* is assigned a name. Names are used to lookup, build and return services.
* 
* A service definition can take one of these forms:
* - A string with a fully qualified class name. This class will be instantiated and returned.
* - A callable. It will be called and the result will be returned.
* - An object. It will be returned as is.
* 
* Factories:
* There is some sort of factory support. In case a service definition is
* an object or a string, it will be checked whether it's an instance of FactoryInterface.
* If so then CreateService method will be called on it and the result will be returned.
*/
class ServiceManager implements ServiceLocatorInterface
{
	/**
	* @var (string|callable|object)[]
	*/
	protected $services = array();

	/**
	* @var object[]
	*/
	protected $instances = array();

	/**
	* Constructs the object. Optionally accepts an array of
	* service name/definition pairs. 
	*
	* @param (string|callable|object)[] $services 
	*/
	function __construct($services = null)
	{
		if($services)
		{
			$this->services = $services;
		}
	}

	/**
	* Sets a service with the specified name. Throws an exception
	* if a service with the same name already exists.
	*
	* @param string $name
	* @param string|callable|object $service
	* @throws InvalidArgumentException
	*/
	function Set($name, $service)
	{
		if($this->Has($name))
		{
			throw new \InvalidArgumentException(sprintf(
				'A service by the name "%s" already exists and cannot be overridden, please use an alternate name.',
				$name
			));
		}
		$this->services[$name] = $service;
	}
	
	/**
	* Returns true if there is a service with the specified name.
	* 
	* @param string $name
	* @return bool
	*/
	function Has($name)
	{
		return isset($this->services[$name]);
	}

	/**
	* Retrieves an instance of the service by name.
	*
	* @param string $name
	* @return object
	* @throws InvalidArgumentException
	*/
	function Get($name)
	{
		if(isset($this->instances[$name]))
		{
			return $this->instances[$name];
		}

        $service = $this->createService($name);
		if(!$service)
		{                 
			throw new \InvalidArgumentException(sprintf(
				'Unable to fetch or create an instance for "%s" service.',
				$name
			));
		}
		$this->instances[$name] = $service;
		return $service;
	}
	
	/**
	* Creates a new instance of the requested service.
	*
	* @param string $name
	* @return object
	* @throws InvalidArgumentException
	*/
	function Create($name)
	{
		$service = $this->createService($name);
		if(!$service)
		{
			throw new \InvalidArgumentException(sprintf(
				'Unable to create an instance for "%s" service.',
				$name
			));
		}
		return $service;
	}

	/**
	* Attempts to create an instance of the requested service.
	*
	* @param string $name
	* @return object|null
	*/
	protected function CreateService($name)
	{
		if(isset($this->services[$name]))
		{
			$service = $this->services[$name];
			
			if(is_callable($service))
			{
				return call_user_func($service, $this);
			}
			if(is_string($service) && class_exists($service, true))
			{
				$service = new $service;
			}
			if(is_object($service))
			{
				if($service instanceof FactoryInterface)
				{
					$service = $service->CreateService($this);
				}
				return $service;
			}
		}
		return null;
	}
}
