<?php

use Marvic\HTTP\Status;

test('Must return "true" if the 200 HTTP status code exists.', function() {
	expect(Status::has(200))->toBe(true);
});

test('Must return the "Not Found" phrase from the 404 HTTP status code.', function() {
	expect(Status::phrase(404))->toBe('Not Found');
});

test('Must list all HTTP response status and your respective phrases.', function() {
	expect(Status::all())->toBeArray();
});
