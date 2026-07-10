<?php

require_once './../../vendor/autoload.php';

$app = Marvic::application();
$app->set('app.folders.uploads', './download');

$app->get('/', function($request, $response) {
	$response->send('<ul>'
		. '<li>Download <a href="/files/lorem.txt">File 1</a></li>'	
		. '<li>Download <a href="/files/marvic.md">File 2</a></li>'	
	. '</ul>');
});

$app->get('/files/{file}', function($request, $response, $next) {
	$file = $request->params('file');

	try {
		$response->download($file);
	} catch (Exception $e) {
		$response->setStatus(404);
		$response->send("Can't find that file. Sorry!");
	}
});

$app->run();
