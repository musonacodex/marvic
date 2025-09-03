<?php

namespace Marvic\HTTP\Message\Request;

/**
 * List of supported HTTP request methods by the Marvic Framework.
 * 
 * @package Marvic\HTTP\Message\Request
 */
final class Methods {
	/** 
	 * List of supported HTTP methods.
	 */
	private const COLLECTION = [
		'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS', 'CONNECT', 'TRACE'
	];

	/** 
	 * List of idempotent HTTP methods.
	 */
	private const IDEMPOTENT = [
		'GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'TRACE'
	];

	/**
	 * Check if the HTTP method exists on the list.
	 * 
	 * @param  string  $method
	 * @return boolean
	 */
	public static function has(string $method): bool {
		return in_array(strtoupper($method), self::COLLECTION);
	}

	/**
	 * Check if the HTTP method is idempotent.
	 * 
	 * @param  string  $method
	 * @return boolean
	 */
	public static function idempotent(string $method): bool {
		return in_array(strtoupper($method), self::IDEMPOTENT);
	}

	/**
	 * Return all HTTP methods.
	 * 
	 * @return arrau
	 */
	public static function all(): array {
		return self::COLLECTION;
	}
}