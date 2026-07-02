<?php

require_once './../../vendor/autoload.php';

$users = [
	['name' => 'John'],
	['name' => 'Peter'],
	['name' => 'Richard'],
];

$app = Marvic::application();

$app->get('/', function($request, $response) use ($users) {
	$response->format([
		'text' => function() use ($response, $users) {
			$callback = fn($user) => ' - ' . $user['name'];
			$text = implode("\n", array_map($callback, $users));
			$response->send("Users:\n$text\n");
		},

		'html' => function() use ($response, $users) {
			$callback = fn($user) => '<li>' . $user['name'] . "</li>";
			$html = implode('', array_map($callback, $users));
			$response->send("<h1>Users</h1><ul>$html</ul>");
		},

		'json' => function() use ($response, $users) {
			$response->send(['users' => json_encode($users)]);
		},
	]);
});

$app->run();
