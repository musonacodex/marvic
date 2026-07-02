<?php

require_once './../../vendor/autoload.php';

$app = Marvic::application();

$app->get('/', function($request, $response) {
	throw new \Exception('Something is wrong!!!');
});

$app->use(function($request, $response, $next) {
	$response->setStatus(404);
	$response->send('Ooops! We does not found the page.');
});

$app->use(function($error, $request, $response, $next) {
	$response->setStatus(500);
	$response->send('Oh no! Someone broke the processing: ' . $error->getMessage());
});

echo $app->request('GET', '/');

