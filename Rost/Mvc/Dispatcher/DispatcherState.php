<?php
namespace Rost\Mvc\Dispatcher;

use Rost\Http\Request;
use Rost\Router\Router;
use Rost\Router\Parameters;
use Rost\View\TemplateManager;

class DispatcherState
{
	/**
	* @var Request
	*/
	public $request;
	
	/**
	* @var Router
	*/
	public $router;
	
	/**
	* @var Parameters
	*/
	public $routeParameters;
	
	/**
	* @var TemplateManager
	*/
	public $templateManager;
}  

