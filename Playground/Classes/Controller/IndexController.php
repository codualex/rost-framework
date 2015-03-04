<?php
namespace Playground\Controller;

use Rost\Mvc\Controller\StandardController;

class IndexController extends StandardController
{
	function DefaultAction()
	{
		$parameters = array(
			'variable' => 'Some Value.'
		);
		return $this->Render('Index', $parameters);
	}
}