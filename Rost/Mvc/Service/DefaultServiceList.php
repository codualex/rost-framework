<?php
namespace Rost\Mvc\Service;

use Rost\Service\ServiceLocatorInterface as Locator;

/**
* A container with the default services for MVC application.
*/
class DefaultServiceList
{
	/**
	* Returns an array with a complete set of the default services.
	* It contains all services required to run MVC application.
	*/
	static function GetServices()
	{
		return [
		
			'dispatcher' => function(Locator $locator)
			{
				return new \Rost\Mvc\Dispatcher\StandardDispatcher($locator);
			},
			
			'router' => function(Locator $locator)
			{
				$configuration = $locator->Get('configuration');
				
				$definitions = $configuration->Get('routes');
				
				$builder = new \Rost\Router\Builder();
				return $builder->CreateRouter($definitions);
			},
			
			'templateManager' => function(Locator $locator)
			{
				$configuration = $locator->Get('configuration');
				
				$directory = $configuration->GetByPath('templateManager/directory');
				$extension = $configuration->GetByPath('templateManager/extension');
				$encoding = $configuration->GetByPath('templateManager/encoding');

				$templateManager = new \Rost\View\TemplateManager($directory, $extension, $encoding);
				\Rost\View\HelperManager::SetTemplateManager($templateManager);
				
				$router = $locator->Get('router');
				\Rost\View\HelperManager::SetRouter($router);
				
				return $templateManager;
			},
			
			'session' => function(Locator $locator)
			{
				$configuration = $locator->Get('configuration');
				$sessionOptions = $configuration->Get('session', []);
				
				return new \Rost\Session\Session($sessionOptions);
			},
			
			'database' => function(Locator $locator)
			{
				$configuration = $locator->Get('configuration');
				$databaseSettings = $configuration->Get('database', []);
				
				return new \Rost\Database\MySql\Database($databaseSettings);
			},
		];
	}
}
