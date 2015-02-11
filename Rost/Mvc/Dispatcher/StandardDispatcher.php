<?php
namespace Rost\Mvc\Dispatcher;

use Rost\Mvc\Dispatcher\DispatcherInterface;
use Rost\Service\ServiceLocatorInterface;
use Rost\Configuration\Configuration;
use Rost\Router\Router;
use Rost\Router\Parameters;
use Rost\Http\Request;
use Rost\Http\Response;
use Rost\Http\Status;
use Rost\Mvc\Dispatcher\DispatcherState;
use Rost\Mvc\Controller\StandardController;
use Rost\View\TemplateManager;

class StandardDispatcher implements DispatcherInterface
{
	/**
	* @var ServiceLocatorInterface
	*/
	protected $serviceLocator;
	
	/**
	* @var Configuration
	*/
	protected $configuration;
	
	/**
	* @var Router
	*/
	protected $router;
	
	/**
	* @var TemplateManager
	*/
	protected $templateManager;
	
	/**
	* @var Parameters
	*/
	protected $routeParameters;
	
	/**
	* @var DispatcherState
	*/
	protected $dispatcherState;
	
	/**
	* Constructs the object.
	* 
	* @param ServiceLocatorInterface $serviceLocator
	*/
	function __construct(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
		$this->configuration = $this->serviceLocator->Get('configuration');
		$this->router = $this->serviceLocator->Get('router');
		$this->templateManager = $this->serviceLocator->Get('templateManager');
		
		$this->dispatcherState = new DispatcherState();
		$this->dispatcherState->router = $this->router;
		$this->dispatcherState->templateManager = $this->templateManager;
	}
	
	/**
	* Dispatches the request.
	* 
	* @param Request $request
	* @return Response
	*/
	function Dispatch(Request $request)
	{
		try
		{
			$this->dispatcherState->request = $request;
			
			$this->routeParameters = $this->router->Match($request);
			if($this->routeParameters == null)
			{
				return $this->DispatchHttpError(Status::NOT_FOUND);
			}
			$this->dispatcherState->routeParameters = $this->routeParameters;
			
			$controllerName = $this->ResolveController();
			$controller = $this->LoadController($controllerName);
			$controller->SetDispatcherState($this->dispatcherState);
			
			$actionName = $this->ResolveAction();
			$method = $this->ConvertActionIntoMethod($actionName);

			return $this->InvokeAction($controller, $method);
		}
		catch(\Exception $exception)
		{
			return $this->DispatchException($exception);
		}
	}

	/**
	* Resolves a controller name.
	* 
	* @return string
	*/
	protected function ResolveController()
	{
		$controllerName = $this->routeParameters->Get('controller');
		if(!$controllerName)
		{
			throw new \RuntimeException(sprintf(
				'The matched route does not have required parameter: "controller".'
			));
		}
		return $controllerName;
	}
	
	/**
	* Loads and instantiates a controller by its name.
	* 
	* @param string $controllerName
	* @return StandardController
	*/
	protected function LoadController($controllerName)
	{
		if($this->serviceLocator->Has($controllerName))
		{
			$controller = $this->serviceLocator->Get($controllerName);
		}
		else if(class_exists($controllerName))
		{
			$controller = new $controllerName;
		}
		else
		{
			throw new \RuntimeException(sprintf(
				'Failed to instantiate "%s" controller. Neither a service nor a class does not exist.',
				$controllerName
			));
		}
		
		if(!$controller instanceof StandardController)
        {
        	throw new \LogicException(sprintf(
				'Could not use "%s" controller because it does not extend a StandardController class.',
				$controllerName
			));
        }
		return $controller;
	}
	
	/**
	* Resolves an action.
	* 
	* @return string
	*/
	protected function ResolveAction()
	{
		return $this->routeParameters->Get('action', 'default');
	}

	/**
	* Converts an action name into a method name.
	*
	* @param string $action
	* @return string
	*/
	protected function ConvertActionIntoMethod($action)
	{
		$method = str_replace(['.', '-'], ' ', $action);
		$method = ucwords($method);
		$method = str_replace(' ', '', $method);
		$method .= 'Action';
		return $method;
	}
	
	/**
	* Invokes the action method on the controller.
	* 
	* @param StandardController $controller
	* @param string $method
	*/
	protected function InvokeAction($controller, $method)
	{
		if(!method_exists($controller, $method))
		{
			throw new \RuntimeException(sprintf(
				'Failed to call an action. "%s" class does not have "%s" method.',
				get_class($controller),
				$method
			));
		}
		
		$arguments = [];
		$reflectedMethod = new \ReflectionMethod($controller, $method);
		foreach($reflectedMethod->getParameters() as $parameter)
		{
			if($this->routeParameters->Has($parameter->name))
			{
				$arguments[] = $this->routeParameters->Get($parameter->name);
			}
			elseif($parameter->isDefaultValueAvailable())
			{
				$arguments[] = $parameter->getDefaultValue();
			}
			else
			{
				throw new \RuntimeException(sprintf(
					'Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).',
					get_class($controller),
					$parameter->name
				));
			}
		}
		return $reflectedMethod->invokeArgs($controller, $arguments);
	}
	
	/**
	* Dispatches the HTTP error.
	* 
	* @param int $statusCode
	* @return Response
	*/
	protected function DispatchHttpError($statusCode)
	{
		$controllerName = $this->configuration->Get('httpErrorController');
		if($controllerName)
		{
			$controller = $this->LoadController($controllerName);
			$controller->SetDispatcherState($this->dispatcherState);
			return $controller->DefaultAction($statusCode);
		}
		return new Response('', 404);
	}
	
	/**
	* Dispatches the exception in the way specified in the configuration.
	* 
	* @param \Exception $exception
	* @return Response
	*/
	protected function DispatchException(\Exception $exception)
	{
		$exceptionControllerName = $this->configuration->Get('exceptionController');
		if($exceptionControllerName)
		{
			$controller = $this->LoadController($exceptionControllerName);
			$controller->SetDispatcherState($this->dispatcherState);
			return $controller->DefaultAction($exception);
		}
		throw $exception;
	}
}
