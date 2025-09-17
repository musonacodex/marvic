<?php

require_once './../../vendor/autoload.php';

$app = Marvic::application();

$users = [
	'list' => function($request, $response) {
		$response->send('User list');
	},

	'get' => function($request, $response) {
		$response->send("User $request->uid");
	},

	'delete' => function($request, $response) {
		$response->send('Delete users');
	},
];

$pets = [
	'list' => function($request, $response) {
		$response->send("User $request->uid pets.");
	},

	'delete' => function($request, $response) {
		$response->send("User $request->uid's pet $request->pid.");
	},
];

$app->map([
	'/users' => [
		'get' => $users['list'],

		'delete' => $users['delete'],

		'/{uid}' => [
			'get' => $users['get'],

			'/pets' => [
				'get' => $pets['list'],

				'/{pid}' => [
					'delete' => $pets['delete'],
				],
			],
		],
	],
]);

print_r( $app->request('DELETE', '/users/123/pets/456') );
