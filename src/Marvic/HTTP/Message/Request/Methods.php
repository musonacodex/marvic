<?php

namespace Marvic\HTTP\Message\Request;

/**
 * List of supported HTTP request methods by the Marvic Framework.
 * 
 * @package Marvic\JTTP
 * @version 1.0.0
 */
final class Methods {
	/** @var string[] */
	private const COLLECTION = [
		'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS', 'CONNECT', 'TRACE'
	];

	private const IDEMPOTENT = [
		'GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'TRACE'
	];

	public static function has(string $method): bool {
		return in_array(strtoupper($method), self::COLLECTION);
	}

	public static function idempotent(string $method): bool {
		return in_array(strtoupper($method), self::IDEMPOTENT);
	}

	public static function all(): array {
		return self::COLLECTION;
	}
}