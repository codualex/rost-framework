<?php
namespace Rost\Service;

/**
* Service locator interface.
*/
interface ServiceLocatorInterface
{
	/**
	* Returns true if there is a service with the specified name.
	* 
	* @param string $name
	* @return bool
	*/
	function Has($name);

	/**
	* Retrieves an instance of the registered service by name.
	*
	* @param string $name
	* @return object
	*/
	function Get($name);
}