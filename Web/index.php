<?php
require('/../Rost/Mvc/Application.php');

//Bootstrap the application using an array with the initial configuration.
//Use an immediately-invoked anonymous function to prevent global variable
//introduction while preparing the initial configuration.
\Rost\Mvc\Application::Bootstrap(call_user_func(function()
	{
		//Get an environment name.
		if(file_exists('environment.php'))
		{
			$environment = include('environment.php');
		}
		elseif(getenv('ROST_ENVIRONMENT'))
		{
			$environment = getenv('ROST_ENVIRONMENT');
		}
		else
		{
			die('Fatal error: Could not find the environment name.');
		}
		
		$applicationDirectory = dirname(__DIR__);
		
		//Prepare the initial configuration array.
		$initialConfiguration = array(
			'classPaths' => array(
				'Rost' => $applicationDirectory . '/Rost',
				'Playground' => $applicationDirectory . '/Playground/Classes'
			),
			'configPathTemplates' => array(
				$applicationDirectory . '/Configuration/*.php',
				$applicationDirectory . '/Configuration/' . $environment . '/*.php'
			)
		);
		return $initialConfiguration;
	}
));
