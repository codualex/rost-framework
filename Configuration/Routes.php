<?php
return [
	'routes' => [

		'index' => [
			'type' => 'literal',
			'path' => '/',
			'parameters' => [
				'controller' => Playground\Controller\IndexController::CLASS
			]
		],

		'users' => [
			'type' => 'pattern',
			'pattern' => '/users[/{action}][/{id}]',
			'parameters' => [
				'controller' => Playground\Controller\UserController::CLASS,
			]
		],

	]
];