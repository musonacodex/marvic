<?php

namespace Marvic\HTTP;

use Exception;
use InvalidArgumentException;
use Marvic\HTTP\Header;
use Marvic\HTTP\Cookie;
use Marvic\HTTP\Kernel as HttpKernel;
use Marvic\HTTP\Header\Collection as Headers;
use Marvic\HTTP\Cookie\Collection as Cookies;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Request\Methods;
use Marvic\HTTP\Message\Response;

/**
 * Marvic HTTP Client
 * 
 * @package Marvic\HTTP
 */
final class Client {
	/**
	 * The HTTP Kernel.
	 *
	 * @var Marvic\HTTP\Jernel
	 */
	private readonly HttpKernel $http;

	/**
	 * The Instance Constructor Method.
	 */
	public function __construct() {
		$this->http = new HttpKernel();
	}

	/**
	 * Send a request to external server using an HTTP method.
	 * 
	 * @param  string $name
	 * @param  array  $arguments
	 * @return Marvic\HTTP\Message\Response
	 */
	public function __call(string $name, array $arguments): Response {
		if ( $name !== strtolower($name) )
			throw new Exception("Inexistent instance method: '$name'");

		return $this->request(strtoupper($name), ...$arguments);
	}

	/**
	 * Request an external server and return a response.
	 *
	 * Allowed Methods:
	 *   GET, POST, PUT, DELETE, HEAD, OPTIONS, TRACE, CONNECT, PATCH.
	 *
	 * Options:
	 *   body (string, array or null)
	 *     - The HTTP request body.
	 *
	 *   cookies (array)
	 *     - A dictionary of HTTP cookies to send with the request.
	 *
	 *   data (array)
	 *     - A dictionary of data to send n body of the request.
	 *
	 *   files (array)
	 *     - A dictionary of file paths to upload with the request.
	 *
	 *   headers (array)
	 *     - A dictionary of HTTP headers to send with the request.
	 *
	 *   json (array)
	 *     - A nested array of data to send as a JSON response.
	 *
	 *   query (array)
	 *     - A not-nested array to send in the URI query string in request.
	 *
	 * @param  string $method
	 * @param  string $uri
	 * @param  array  $options
	 * @return Marvic\HTTP\Message\Response
	 */
	public function request(string $method, string $uri, ...$options): Response {
		if ( is_array($options) && array_is_list($options) ) $options = $options[0];

		if (! Methods::has($method) ) {
			$message = "Inexistent HTTP request method: '$method'";
			throw new InvalidArgumentException($message);
		}

		if ( isset($options['query']) ) {
			$query = http_build_query($options['query']);
			$uri   = str_contains($url, '?') ? "&$query" : "?$query";
			unset($options['query']);
		}

		if (isset($options['headers']) && array_is_list($options['headers'])) {
			$message = "The 'headers' option must be associative array";
			throw new InvalidArgumentException($message);
		} else {
			$options['headers'] = [];
		}

		if (isset($options['cookies']) || array_is_list($options['cookies'])) {
			$message = "The 'cookies' option must be associative array";
			throw new InvalidArgumentException($message);
		} else {
			$options['cookies'] = [];
		}

		if ( isset($options['json']) && !isset($options['body']) ) {
			$options['body'] = json_encode($options['json']);
			$options['headers']['Content-Type'] = 'application/json';
			$options['headers']['Content-Length'] = strlen($options['body']);
			unset($options['data']);
		}

		if ( isset($options['files']) && !isset($options['body']) ) {
			if ( isset($options['data']) )
				$options['files'] = array_merge($options['data'], $options['files']);

			$boundary = '------------WebKitFormUploadFIleBoundaryxYz123';
			$options['headers']['Content-Type']  = 'multipart/form-data; boundary=';
			$options['headers']['Content-Type'] += $boundary;

			$options['body'] = $body = '';
			foreach ($options['files'] as $name => $value) {
				$body += "$boundary\r\n";
				$body += "Content-Disposition: form-data; name=\"$name\"";

				if ( file_exists($value) ) {
					$filename  = basename($value);
					$extension = pathinfo($value, PATHINFO_EXTENSION);
					$mimetype  = MimeTypes::mimetype($extension, 'application/octet-stream');

					$body += "; filename=\"$filename.$extension\"\r\n";
					$body += "Content-Type: $mimetype";
					$body += "\r\n\r\n" . file_get_contents($value) . "\r\n";
				} else {
					$body += "\r\n\r\n$value\r\n";
				}
				$options['body'] += $body;
			}
			$options['body'] += "$boundary--";
		}

		if ( isset($options['data']) && !isset($options['body']) ) {
			$options['body'] = http_build_query($options['data']);
			$options['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
			$options['headers']['Content-Length'] = strlen($options['body']);
			unset($options['data']);
		}

		if ( isset($options['body']) && !isset($options['headers']['Content-Type']) ) {
			if ( is_string($options['body']) ) {
				$options['headers']['Content-Type'] = 'text/html; charset=UTF-8';
				$options['headers']['Content-Length'] = strlen($options['body']);
			}
			else if ( is_array($options['body']) ) {
				$options['body'] = json_encode($options['body']);
				$options['headers']['Content-Type'] = 'application/json; charset=UTF-8';
				$options['headers']['Content-Length'] = strlen($options['body']);
			}
		} else if (! isset($options['body']) ) {
			$options['body'] = '';
		}

		$request = $this->http->newRequest(null, $method, $uri, $options);
		return $this->http->send($request);
	}
}
