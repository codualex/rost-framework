<?php
namespace Rost\Service;

/**
* Service factory interface.
*/
interface FactoryInterface
{
	/**
	* Creates a service.
	*
	* @param ServiceLocatorInterface $serviceLocator
	* @return object
	*/
	function CreateService(ServiceLocatorInterface $serviceLocator);
}