<?php

use Marvic\HTTP\Header;
use Marvic\HTTP\Header\Collection;

$collection = new Collection(...array_map(fn($item) => new Header(...$item),
	[
		['Content-Type', 'text/html, charset=UTF-8'],
		['Content-Length', '12'],
		['Location', 'https://www.google.com/'],
	]
));


test('A collection must get headers correctly', function($collection) {
	expect($collection->get('Content-Type'))->toBe('text/html, charset=UTF-8');
	expect($collection->get('Content-Length'))->toBe('12');
	expect($collection->get('Location'))->toBe('https://www.google.com/');

})->with($collection);

test('A collection must set or add headers correctly', function($collection) {
	$collection->set('Content-Type', 'application/json');
	expect($collection->get('Content-Type'))->toBe('application/json');
	
	$collection->set('Links', 'https://facebook.com');
	expect($collection->get('Links'))->toBe('https://facebook.com');

})->with($collection);

test('A collection must check headers correctly', function($collection) {
	expect($collection->has('Content-Type'))->toBe(true);
	expect($collection->is('Content-Length', '13'))->toBe(false);
	expect($collection->is('Content-Length', '12'))->toBe(true);

})->with($collection);

test('A collection must remove headers correctly', function($collection) {
	expect($collection->has('Content-Type'))->toBe(true);

	$collection->remove('Content-Type');
	expect($collection->has('Content-Type'))->toBe(false);

})->with($collection);
