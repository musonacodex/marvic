<?php

require_once './../../vendor/autoload.php';

$app = Marvic::application();

$app->get('/', function($request, $response) {
	throw new Exception('Something is broke!');
});

$app->use(function($request, $response, $next) {
	$response->setStatus(404);
	$response->send('404 Not Found');
});

$app->use(function($error, $request, $response, $next) {
	$response->setStatus(500);
	$response->send('500 Internal Server Error: ' . $error->getMessage());
});

$app->run();
