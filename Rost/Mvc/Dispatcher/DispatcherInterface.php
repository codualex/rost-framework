<?php
namespace Rost\Mvc\Dispatcher;

use Rost\Http\Request;
use Rost\Http\Response;

interface DispatcherInterface
{
	/**
	* Dispatches the request.
	* 
	* @param Request $request
	* @return Response $response
	*/
	function Dispatch(Request $request);
}