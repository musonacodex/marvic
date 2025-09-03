<?php

namespace Marvic\HTTP\Message;

use Marvic\Application;
use Marvic\HTTP\Header;
use Marvic\HTTP\Cookie;
use Marvic\HTTP\Session;
use Marvic\HTTP\Header\Collection as Headers;
use Marvic\HTTP\Cookie\Collection as Cookies;
use Marvic\HTTP\Message\Request\Url;

/**
 * HTTP Message Factory (either request or response).
 *
 * @package Marvic\HTTP\Message
 */
final class Factory {
	/**
	 * Internal: Capture avaliable IP address from the current request.
	 * 
	 * @return array
	 */
	private function captureIpAddresses(): array {
		$keys = ['REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
			'HTTP_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_CF_CONNECTING_IP', // CloudFare
			'HTTP_X_REAL_IP',
		];

		$ips = [];
		foreach ($keys as $key) {
			if ( empty($_SERVER[$key]) ) continue;
			$list = explode(',', $_SERVER[$key]);

			foreach ($list as $ip) {
				$ip = trim($ip);
				if ( filter_var($ip, FILTER_VALIDATE_IP) )
					$ips[] = $ip;
			}
		}
		return array_unique($ips);
	}

	/**
	 * Internal: Capture the HTTP version from the server.
	 * 
	 * @return string
	 */
	private function captureHttpVersion(): string {
		return $_SERVER['SERVER_PROTOCOL'];
	}

	/**
	 * Internal: Capture the HTTP Request Method from the server.
	 * 
	 * @param  string $trustParam
	 * @return string
	 */
	private function captureMethod(string $trustParam = '_method_'): string {
		$method = strtoupper($_SERVER['REQUEST_METHOD']);
		if ($method === 'POST' && isset($_POST[$trustParam]))
			$method = $_POST[$trustParam];
		return $method;
	}

	/**
	 * Internal: Capture the HTTP Request URL from the server.
	 * 
	 * @return Marvic\HTTP\Message\Request\Url
	 */
	private function captureUrl(): Url {
		$safe = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
		$url  = ( $safe ) ? 'https://' : 'http://';
		$url .= $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];
		return new Url($url);
	}

	/**
	 * Internal: Capture the HTTP Request Headers from the server.
	 * 
	 * @return Marvic\HTTP\Header\Collection
	 */
	private function captureHeaders(): Headers {
		$headers = 
			function_exists('getallheaders') ? getallheaders() : $_SERVER;

		foreach ($headers as $key => $value) {
			$newkey = ucwords(str_replace(['HTTP_','_'], ['','-'], $key), '-');
			$headers[] = new Header($newkey, $value); unset($headers[$key]);
		}
		return new Headers(...$headers);
	}

	/**
	 * Internal: Capture the HTTP Request Cookies from the server.
	 * 
	 * @return Marvic\HTTP\Cookie\Collection
	 */
	private function captureCookies(): Cookies {
		$cookies = new Cookies();
		foreach ($_COOKIE as $key => $value)
			$cookies->set($key, $value);
		return $cookies;
	}

	/**
	 * Internal: Capture the HTTP Request Body from the server.
	 * 
	 * @return mixed
	 */
	private function captureBody(): mixed {
		$body   = null;
		$method = $this->captureMethod();
		$type   = '';

		if ( isset($_SERVER['HTTP_CONTENT_TYPE']) )
			$type = trim(explode(';', $_SERVER['HTTP_CONTENT_TYPE'])[0]);

		if ($method === 'POST') {
			switch ( $type ) {
				case 'application/x-www-form-urlencoded':
					return $_POST;
					break;
				
				case 'multipart/form-data':
					return $_POST . $_FILES;
					break;
			}
		}
		return file_get_contents('php://input');
	}

	/**
	 * Create a new HTTP Request Instance.
	 * 
	 * @return HTTP\Message\Request
	 */
	public function newRequest(?Application $app, string $method, 
		string $url, array $options = []): Request
	{
		$url     = new Url($url);
		$headers = new Headers();
		$cookies = new Cookies();
		$body    = '';

		$headers->set('Host', $url->hostname);
		$headers->set('Connection', 'Close');
		$headers->set('Content-Length', strlen($body));

		if ( isset($options['headers']) ) {
			foreach ($options['headers'] as $key => $value)
				$headers->set($key, $value);
		}
		if ( isset($options['cookies']) ) {
			foreach ($options['cookies'] as $key => $value)
				$cookies->set($key, $value);
		}

		return new Request($app, $method, $url, [
			'version' => 'HTTP/1.1',
			'headers' => $headers,
			'cookies' => $cookies,
			'body'    => $body,
		]);
	}

	/**
	 * Capture the HTTP Request from the server.
	 * 
	 * @return HTTP\Message\Request
	 */
	public function captureRequest(Application $app): Request {
		$method = $this->captureMethod();
		$url    = $this->captureUrl();
		$body   = $this->captureBody();

		return new Request($app, $method, $url, [
			'ips'     => $this->captureIpAddresses(),
			'input'   => is_array($body) ? $body : [],
			'session' => new Session(),
			'version' => $this->captureHttpVersion(),
			'headers' => $this->captureHeaders(),
			'cookies' => $this->captureCookies(),
			'body'    => is_string($body) ? $body : '',
		]);
	}

	/**
	 * Create a new HTTP Response Instance
	 * .
	 * @return HTTP\Message\Response
	 */
	public function newResponse(Request $request, int $status = 200,
		array $options = []): Response
	{
		return new Response($request, $status, $options);
	}
}
