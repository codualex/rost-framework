<?php

//Set error reporting to the level to which an application must comply.
error_reporting(E_ALL | E_NOTICE);

call_user_func(function()
	{
		//Check PHPUnit version.
		$version = PHPUnit_Runner_Version::id();
		if(version_compare($version, '4.3.0', '<'))
		{
			exit("This version of PHPUnit ($version) is not supported in Rost unit tests.");
		}
		
		$applicationDirectory = dirname(__DIR__);
		
		//Setup class autoloading.
		require($applicationDirectory . '/Rost/Loader/ClassLoader.php');
		$classLoader = new \Rost\Loader\ClassLoader();
		$classLoader->RegisterNamespace('Rost', $applicationDirectory . '/Rost');
		$classLoader->RegisterNamespace('Tests', $applicationDirectory . '/Tests');
		$classLoader->Register();
	}
);
