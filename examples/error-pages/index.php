<?php

require_once './../../vendor/autoload.php';

$app = Marvic::application();
$app->set('folders.views', './views');

$app->get('/', function($request, $response) {
	$response->render('index.html');
});

$app->get('/500', function($request, $response, $next) {
	$next(new Exception('Webserver throw a fatal error.'));
});

$app->use(function($request, $response, $next) {
	$response->setStatus(404);
	$response->render('notFound.html');
});

$app->use(function($error, $request, $response, $next) {
	$response->setStatus(500);
	$response->render('serverError.php', ['error' => $error]);
});

$app->run();
