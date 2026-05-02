<?php

require_once './../../vendor/autoload.php';

$app = Marvic::application();

$app->get('/', function($request, $response) {
	$response->send('Hello World!');
});

$app->run();
