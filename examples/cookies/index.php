<?php

require_once './../../vendor/autoload.php';

$app = Marvic::application();

$app->get('/', function($request, $response) {
	if ($request->hasCookie('remember')) {
		$response->send('Remembered! Click to <a href="/forget">forget</a>');
	}
	else {
		$response->send('<form action="/" method="POST"> Check to '
			. '<input type="checkbox" id="remember" name="remember" />'
			. '<label for="remember">remember me</label>'
			. '<button type="submit">Submit</button></form>'
		);
	}
});

$app->get('/forget', function($request, $response) {
	$response->removeCookie('remember');
	$response->redirect($request->get('Referrer', '/'));
});

$app->post('/', function($request, $response) {
	$minutes = 60000;
	if ($request->input('remember') === 'on')
		$response->setCookie('remember', 1, ['maxAge' => $minutes]);
	$response->redirect($request->get('Referrer', '/'));
});

$app->run();
