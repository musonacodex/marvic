<?php

use Marvic\HTTP\MimeTypes;

test('Must get the informations about the JSON file.', function() {
	expect(MimeTypes::getType('.json'))->toBe('application/json');

	expect(MimeTypes::getName('.json'))->toBe('JavaScript Object Notation (JSON)');

	expect(MimeTypes::getExtension('application/json'))->toBe('json');
});

test('Must check the mimetype and extension of the Zip file.', function() {
	expect(MimeTypes::hasType('application/json'))->toBe(true);

	expect(MimeTypes::hasExtension('json'))->toBe(true);
});
