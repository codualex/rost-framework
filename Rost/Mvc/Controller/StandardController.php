<?php
namespace Rost\Mvc\Controller;

use Rost\Mvc\Dispatcher\DispatcherState;
use Rost\Http\Request;
use Rost\Http\Response;
use Rost\Http\RedirectResponse;

/**
* This is a required base class for all controllers which should to be
* dispatchable by the StandardDispatcher. In addition it has shortcut
* methods to run common operations.
*/
abstract class StandardController
{
	/**
	* @var DispatcherState
	*/
	protected $dispatcherState;
	
	/**
	* Sets a dispatcher state.
	* 
	* @param DispatcherState $dispatcherState
	*/
	function SetDispatcherState(DispatcherState $dispatcherState)
	{
		$this->dispatcherState = $dispatcherState;
	}
	
	/**
	* Returns Request instance.
	* 
	* @return Request
	*/
	protected function GetRequest()
	{
		return $this->dispatcherState->request;
	}
	
	/**
	* Renders a template and returns Response object with the rendered content.
	* 
	* @param string $template
	* @param mixed[] $variables
	* @return Response
	*/
	protected function Render($template, $variables = [])
	{
		$templateManager = $this->dispatcherState->templateManager;
		$templateManager->SetGlobalVariables($variables);
		$content = $templateManager->Render($template);
		
		return new Response($content);
	}
	
	/**
	* Returns a named parameter from the matched route.
	* Returns the default value if there is no parameter with the given name.
	*  
	* @param string $name
	* @param mixed $default
	* @return mixed
	*/
	protected function GetRouteParameter($name, $default = null)
	{
		return $this->dispatcherState->routeParameters->Get($name, $default);
	}
	
	/**
	* Returns a named POST parameter.
	* Returns the default value if there is no parameter with the given name.
	*  
	* @param string $name
	* @param mixed $default
	* @return mixed
	*/
	protected function GetPostParameter($name, $default = null)
	{
		$request = $this->GetRequest();
		return $request->GetPostParameters()->Get($name, $default);
	}
	
	/**
	* Returns a named parameter from the URL query.
	* Returns the default value if there is no parameter with the given name.
	*  
	* @param string $name
	* @param mixed $default
	* @return mixed
	*/
	protected function GetQueryParameter($name, $default = null)
	{
		$request = $this->GetRequest();
		return $request->GetQueryParameters()->Get($name, $default);
	}
	
	/**
	* Generates URL by the given route name and parameters.
	* 
	* @param string $routeName
	* @param string[] $parameters
	* @return string
	*/
	protected function Url($routeName, $parameters = [])
	{
		return $this->dispatcherState->router->Assemble($routeName, $parameters);
	}

	/**
	* Returns the initialized RedirectResponce object.
	* 
	* @param string $url
	* @param int $statusCode The HTTP status code.
	* @return RedirectResponse
	*/
	protected function Redirect($url, $statusCode = 301)
	{
		return new RedirectResponse($url, $statusCode);
	}
	
	/**
	* Returns the initialized RedirectResponce object.
	* 
	* @param string $routeName
	* @param string[] $parameters
	* @param int $statusCode The HTTP status code.
	* @return RedirectResponse
	*/
	protected function RedirectToRoute($routeName, $parameters = [], $statusCode = 301)
	{
		$url = $this->Url($routeName, $parameters);
		return $this->Redirect($url, $statusCode);
	}
}
