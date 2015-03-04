<?php
use Rost\Service\ServiceLocatorInterface as Locator;

return [
	'services' => [

		'Playground\Controller\IndexController' => function(Locator $locator)
		{
			return new \Playground\Controller\IndexController();
		},
		
		'Playground\Controller\UserController' => function(Locator $locator)
		{
			$database = $locator->Get('database');
			$database->Connect();
			return new \Playground\Controller\UserController($database);
		}
	]
];