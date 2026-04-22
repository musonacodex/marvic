<?php

namespace Marvic\Http\Message\Request;

use InvalidArgumentException;

final class Methods {
	// HTTP Methods Constants
	public const CONNECT = 'CONNECT';
	public const DELETE  = 'DELETE';
	public const GET     = 'GET';
	public const HEAD    = 'HEAD';
	public const OPTIONS = 'OPTIONS';
	public const PATCH   = 'PATCH';
	public const POST    = 'POST';
	public const PUT     = 'PUT';
	public const TRACE   = 'TRACE';
	
	// All valid methods for validation
	private const VALID_METHODS = [
		self::GET,
		self::HEAD,
		self::POST,
		self::PUT,
		self::DELETE,
		self::CONNECT,
		self::OPTIONS,
		self::TRACE,
		self::PATCH
	];
	
	// Idempotent methods (same request multiple times = same result)
	private const IDEMPOTENT_METHODS = [
		self::GET,
		self::HEAD,
		self::PUT,
		self::DELETE,
		self::OPTIONS,
		self::TRACE
	];
	
	// Safe methods (no side effects on server state)
	private const SAFE_METHODS = [
		self::GET,
		self::HEAD,
		self::OPTIONS,
		self::TRACE
	];

	public static function all(): array {
		return self::VALID_METHODS;
	}

	public static function has(string $method): bool {
		return in_array(strtoupper($method), self::VALID_METHODS, true);
	}

	public static function idempotent(string $method): bool {
		if (! self::has($method) ) return false;
		return in_array(strtoupper($method), self::IDEMPOTENT_METHODS, true);
	}

	public static function safe(string $method): bool {
		if (! self::has($method) ) return false;
		return in_array(strtoupper($method), self::SAFE_METHODS, true);
	}
	
	public static function getIdempotentMethods(): array {
		return self::IDEMPOTENT_METHODS;
	}

	public static function getSafeMethods(): array {
		return self::SAFE_METHODS;
	}

	public static function cacheable(string $method): bool {
		return self::safe($method) || $method === self::POST;
	}

	public static function validateOrFail(string $method): void {
		if ( self::has($method) ) return;
		$message  = "Invalid HTTP method: \"$method\". ";
		$message .= "Valid methods are: ". implode(', ', self::VALID_METHODS);
		throw new InvalidArgumentException($message);
	}

	public static function getCharacteristics(string $method): array {
		return [
			'method'     => strtoupper($method),
			'has'        => self::has($method),
			'safe'       => self::safe($method),
			'idempotent' => self::idempotent($method),
			'cacheable'  => self::cacheable($method)
		];
	}
}