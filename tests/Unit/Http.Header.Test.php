<?php

use Marvic\HTTP\Header;

$headers = [
	['Content-Type', 'text/html, charset=UTF-8'],
	['Content-Length', '12'],
	['Location', 'https://www.google.com/'],
];

test('A header must instance correctly', function($name, $value) {
	$header = new Header($name, $value);

	expect($header->name)->toBe($name);
	expect($header->value)->toBe($value);

	expect("$header")->toBeString();

	expect($header->toArray())->toBeArray();

})->with($headers);
