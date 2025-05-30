<?php

namespace Marvic\HTTP;

/**
 * List of supported HTTP request methods by the Marvic Framework.
 * 
 * @package Marvic\JTTP
 * @version 1.0.0
 */
final class Methods {
	/** @var string[] */
	private static array $collection = [
		'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS', 'CONNECT', 'TRACE'
	];

	public static function has(string $method): bool {
		return in_array(strtoupper($method), self::$collection);
	}

	public static function all(): array {
		return self::$collection;
	}
}