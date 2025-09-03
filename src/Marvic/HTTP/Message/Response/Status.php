<?php

namespace Marvic\HTTP\Message\Response;

/**
 * List of supported HTTP response status by the Marvic Framework.
 * 
 * @package Marvic\HTTP\Message\Response
 */
final class Status {
	/** 
	 * The list of allowed HTTP Statuses
	 * 
	 * @var array<integer, string>
	 */
	private static array $collection = [
		
		// 1xx Informational
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		103 => 'Early Hints',

		// 2xx Success
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',

		// 3xx Redirection
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',

		// 4xx Client Error
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Payload Too Large',
		414 => 'URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		421 => 'Misdirected Request',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Too Early',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		451 => 'Unavailable For Legal Reasons',

		// 5xx Server Error
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
	];

	/**
	 * Check if the status code exists on the list.
	 * 
	 * @param  int     $code
	 * @return boolean
	 */
	public static function has(int $code): bool {
		return array_key_exists($code, self::$collection);
	}

	/**
	 * Check if the number is an information code.
	 * 
	 * @param  int     $code
	 * @return boolean
	 */
	public static function isInformation(int $code): bool {
		return self::has($code) && 100 <= $code && $code < 200;
	}

	/**
	 * Check if the number is a success code.
	 * 
	 * @param  int     $code
	 * @return boolean
	 */
	public static function isSuccess(int $code): bool {
		return self::has($code) && 200 <= $code && $code < 300;
	}

	/**
	 * Check if the number is a redirection code.
	 * 
	 * @param  int     $code
	 * @return boolean
	 */
	public static function isRedirection(int $code): bool {
		return self::has($code) && 300 <= $code && $code < 400;
	}

	/**
	 * Check if the number is a client error code.
	 * 
	 * @param  int     $code
	 * @return boolean
	 */
	public static function isClientError(int $code): bool {
		return self::has($code) && 400 <= $code && $code < 500;
	}

	/**
	 * Check if the number is a server error code.
	 * 
	 * @param  int     $code
	 * @return boolean
	 */
	public static function isServerError(int $code): bool {
		return self::has($code) && 500 <= $code && $code < 600;
	}

	/**
	 * Get the status phrase from a status code.
	 * 
	 * @param  int         $code
	 * @return string|null
	 */
	public static function phrase(int $code): ?string {
		return self::$collection[$code] ?? null;
	}

	/**
	 * Return all HTTP status.
	 * 
	 * @return array
	 */
	public static function all(): array {
		return self::$collection;
	}
}