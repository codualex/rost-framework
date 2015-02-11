<?php
namespace Rost\Mvc;

use \Rost\Loader\ClassLoader;
use \Rost\Configuration\Configuration;
use \Rost\Mvc\Service\DefaultServiceList;
use \Rost\Service\ServiceManager;
use \Rost\Http\Request;
use \Rost\Http\Response;

class Application
{
	/**
	* @var ClassLoader
	*/
	protected $classLoader;
	
	/**
	* @var Configuration
	*/
	protected $configuration;
	
	/**
	* @var ServiceManager
	*/
	protected $serviceManager;
	
	/**
	* Bootstraps and runs the application.
	* 
	* @param string[][] $configuration Initial configuration.
	*/
	static function Bootstrap(array $configuration)
	{
		$defaultConfiguration = [
			'classPaths' => [],
			'configPathTemplates' => []
		];
		$configuration = array_merge($defaultConfiguration, $configuration);
		
		$application = new static;
		$application->InitClassLoader($configuration['classPaths']);
		$application->InitConfiguration($configuration['configPathTemplates']);
		$application->InitServiceManager();
		
		$application->Run();
	}
	
	/**
	* Initializes an automatic class loader.
	* 
	* @param string[] $namespaces Namespace/directory pairs.
	*/
	protected function InitClassLoader($namespaces)
	{
		require(dirname(__DIR__) . '/Loader/ClassLoader.php');

		$this->classLoader = new ClassLoader();
		foreach($namespaces as $namespace => $directory)
		{
			$this->classLoader->RegisterNamespace($namespace, $directory);
		}
		$this->classLoader->Register();
	}

	/**
	* Initializes a configuration container.
	* 
	* @param string[] $pathTemplates Configuration path templates.
	*/
	protected function InitConfiguration($pathTemplates)
	{
		$this->configuration = new Configuration(); 
		foreach($pathTemplates as $template)
		{
			foreach(glob($template, GLOB_NOSORT) as $filename)
			{
				$this->configuration->Merge(include($filename));
			}  
		}
	}
	
	/**
	* Initializes a service manager.
	*/
	protected function InitServiceManager()
	{
		$services = $this->configuration->Get('services');		
		$defaultServices = DefaultServiceList::GetServices();
		$services = array_merge($defaultServices, $services);
		
		$this->serviceManager = new ServiceManager($services); 
		$this->serviceManager->Set('classLoader', $this->classLoader);
		$this->serviceManager->Set('configuration', $this->configuration);
	}

	/**
	* Runs the application.
	*/
	protected function Run()
	{
		$request = Request::CreateFromEnvironment();
		
		$dispatcher = $this->serviceManager->Get('dispatcher');
		$response = $dispatcher->Dispatch($request);
		
		$response->Send();
	}
}
