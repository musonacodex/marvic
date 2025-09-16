<?php

require_once './../../vendor/autoload.php';

$app = Marvic::application();

$app->get('/', function($request, $response) {
	$response->send('<h1>Hello World!</h1>');
});

$app->run();
