<?php

use Framework\Http\Message\Request\Uri\Uri;

test('parses complete URL with all components', function () {
	$uri = new Uri('https://user:pass@example.com:8080/path/to/page?q1=value1&q2=value2#section');

	expect($uri->scheme)->toBe('https')
		->and($uri->username)->toBe('user')
		->and($uri->password)->toBe('pass')
		->and($uri->host)->toBe('example.com')
		->and($uri->port)->toBe(8080)
		->and($uri->path)->toBe('/path/to/page')
		->and($uri->query)->toBe('q1=value1&q2=value2')
		->and($uri->fragment)->toBe('section');
});

test('parses URL with only scheme and host', function () {
	$uri = new Uri('http://example.com');

	expect($uri->scheme)->toBe('http')
		->and($uri->host)->toBe('example.com')
		->and($uri->username)->toBeNull()
		->and($uri->password)->toBeNull()
		->and($uri->port)->toBeNull()
		->and($uri->path)->toBeNull()
		->and($uri->query)->toBeNull()
		->and($uri->fragment)->toBeNull();
});

test('parses URL without scheme (relative URL)', function () {
	$uri = new Uri('/relative/path?query=value#fragment');

	expect($uri->scheme)->toBeNull()
		->and($uri->host)->toBeNull()
		->and($uri->path)->toBe('/relative/path')
		->and($uri->query)->toBe('query=value')
		->and($uri->fragment)->toBe('fragment');
});

test('parses URL with default HTTP port', function () {
	$uri = new Uri('http://example.com:80/path');

	expect($uri->host)->toBe('example.com')
		->and($uri->port)->toBe(80);
});

test('parses URL with default HTTPS port', function () {
	$uri = new Uri('https://example.com:443/path');

	expect($uri->host)->toBe('example.com')
		->and($uri->port)->toBe(443);
});

test('parses URL with only username', function () {
	$uri = new Uri('https://user@example.com');

	expect($uri->username)->toBe('user')
		->and($uri->password)->toBeNull()
		->and($uri->user)->toBe('user');
});

test('parses URL with empty authority', function () {
	$uri = new Uri('file:///path/to/file');

	expect($uri->scheme)->toBe('file')
		->and($uri->host)->toBeNull()
		->and($uri->path)->toBe('/path/to/file');
});

test('throws exception for invalid URL', function () {
	expect(fn() => new Uri('://invalid'))
		->toThrow(InvalidArgumentException::class, 'Invalid URL');
});

test('builds user info correctly', function () {
	$uri = new Uri('https://user:pass@example.com');
	expect($uri->user)->toBe('user:pass');

	$uri = new Uri('https://user@example.com');
	expect($uri->user)->toBe('user');

	$uri = new Uri('https://example.com');
	expect($uri->user)->toBe('');
});

test('builds authority correctly', function () {
	$uri = new Uri('https://user:pass@example.com:8080');
	expect($uri->authority)->toBe('user:pass@example.com:8080');

	$uri = new Uri('https://user@example.com');
	expect($uri->authority)->toBe('user@example.com');

	$uri = new Uri('https://example.com:8080');
	expect($uri->authority)->toBe('example.com:8080');

	$uri = new Uri('https://example.com');
	expect($uri->authority)->toBe('example.com');

	$uri = new Uri('/relative/path');
	expect($uri->authority)->toBe('');
});

test('builds full path correctly', function () {
	$uri = new Uri('/path?query=value#fragment');
	expect($uri->fullpath)->toBe('/path?query=value#fragment');

	$uri = new Uri('/path?query=value');
	expect($uri->fullpath)->toBe('/path?query=value');

	$uri = new Uri('/path#fragment');
	expect($uri->fullpath)->toBe('/path#fragment');

	$uri = new Uri('/path');
	expect($uri->fullpath)->toBe('/path');

	$uri = new Uri('');
	expect($uri->fullpath)->toBe('');
});

test('builds base URL correctly', function () {
	$uri = new Uri('https://user:pass@example.com:8080/path');
	expect($uri->baseurl)->toBe('https://user:pass@example.com:8080');

	$uri = new Uri('http://example.com/path');
	expect($uri->baseurl)->toBe('http://example.com');

	$uri = new Uri('/relative/path');
	expect($uri->baseurl)->toBe('');
});

test('builds full URL correctly', function () {
	$uri = new Uri('https://example.com:8080/path?query=value#fragment');
	expect($uri->fullurl)->toBe('https://example.com:8080/path?query=value#fragment');

	$uri = new Uri('/relative/path?q=1');
	expect($uri->fullurl)->toBe('/relative/path?q=1');
});

test('parses query parameters correctly', function () {
	$uri = new Uri('/path?name=João&age=30&active=true');

	expect($uri->query('name'))->toBe('João')
		->and($uri->query('age'))->toBe('30')
		->and($uri->query('active'))->toBe('true')
		->and($uri->query('nonexistent'))->toBeNull()
		->and($uri->query('nonexistent', 'default'))->toBe('default');
});

test('parses query with array syntax', function () {
	$uri = new Uri('/path?filter[name]=john&filter[age]=30');

	expect($uri->query('filter'))->toBe([
		'name' => 'john',
		'age' => '30'
	]);
});

test('parses query with encoded characters', function () {
	$uri = new Uri('/path?q=hello%20world&name=%C3%A9dipo');

	expect($uri->query('q'))->toBe('hello world')
		->and($uri->query('name'))->toBe('édipo');
});

test('returns all queries as array', function () {
	$uri = new Uri('/path?name=John&age=30&city=NYC');

	expect($uri->allQueries())->toBe([
		'name' => 'John',
		'age' => '30',
		'city' => 'NYC'
	]);

	$uri = new Uri('/path');
	expect($uri->allQueries())->toBe([]);
});

test('handles empty query string', function () {
	$uri = new Uri('/path?');

	expect($uri->query)->toBe('')
		->and($uri->allQueries())->toBe([])
		->and($uri->query('any'))->toBeNull();
});

test('handles multiple question marks (only first is query)', function () {
	$uri = new Uri('/path?q=1?invalid?test');

	expect($uri->path)->toBe('/path')
		->and($uri->query)->toBe('q=1?invalid?test');
});

test('extracts port correctly from different schemes', function () {
	$http = new Uri('http://example.com:8080');
	$https = new Uri('https://example.com:8443');
	$ftp = new Uri('ftp://example.com:21');

	expect($http->port)->toBe(8080)
		->and($https->port)->toBe(8443)
		->and($ftp->port)->toBe(21);
});

test('handles IPv6 host addresses', function () {
	$uri = new Uri('https://[2001:db8::1]:8080/path');

	expect($uri->host)->toBe('[2001:db8::1]')
		->and($uri->port)->toBe(8080)
		->and($uri->authority)->toBe('[2001:db8::1]:8080');
});

test('handles subdomains correctly', function () {
	$uri = new Uri('https://subdomain.example.com/path');

	expect($uri->host)->toBe('subdomain.example.com')
		->and($uri->authority)->toBe('subdomain.example.com');
});

test('preserves original path with leading slash', function () {
	$uri = new Uri('https://example.com/path/to/page');

	expect($uri->path)->toBe('/path/to/page');
});

test('handles root path', function () {
	$uri = new Uri('https://example.com/');

	expect($uri->path)->toBe('/');
});

test('handles empty path', function () {
	$uri = new Uri('https://example.com');

	expect($uri->path)->toBeNull();
});

test('query method returns null for non-existent key without default', function () {
	$uri = new Uri('/path?name=John');

	expect($uri->query('age'))->toBeNull();
});

test('query method returns default for non-existent key with default', function () {
	$uri = new Uri('/path?name=John');

	expect($uri->query('age', '25'))->toBe('25');
});