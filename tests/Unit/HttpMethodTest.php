<?php

use Marvic\HTTP\Methods;

test('Must list all supported HTTP methods.', function() {
	expect(Methods::all())->toBeArray();
});

test('Must return "true" if the GET method exists.', function() {
	expect(Methods::has('GET'))->toBe(true);
});