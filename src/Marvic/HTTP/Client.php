<?php

namespace Marvic\HTTP;

use Exception;

use Marvic\HTTP\Kernel as HttpKernel;
use Marvic\HTTP\Header\Collection as Headers;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Request\Methods;
use Marvic\HTTP\Message\Response;

/**
 * HTTP Client
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
	 * Send a request to an external server with an HTTP method.
	 * 
	 * @param  string $name
	 * @param  array  $arguments
	 * @return Marvic\HTTP\Message|Response
	 */
	public function __call(string $name, array $arguments): Response {
		if (! in_array(strtoupper($name), Methods::all()) )
			throw new Exception("Inexistent instance method: $name");

		$method = strtoupper($name);
		$uri    = array_shift($arguments);

		if (! is_string($uri) ) 
			throw new Exception("The url must be string");

		$body    = !empty($arguments) ? array_shift($arguments) : [];
		$headers = !empty($arguments) ? array_shift($arguments) : [];
		$cookies = !empty($arguments) ? array_shift($arguments) : [];

		if ( empty($body) && is_array($body) ) {
			$body = http_build_query($body);
			$headers['Content-Type'] = 'application/x-www-form-urlencoded';
			$headers['Content-Length'] = strlen($body);
		}

		$request = $this->http->newRequest(null, $method, $uri, [
			'headers' => $headers,
			'cookies' => $cookies,
			'body'    => $body,
		]);
		return $this->http->send($request);
	}
}