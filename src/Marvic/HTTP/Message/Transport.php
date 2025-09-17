<?php

namespace Marvic\HTTP\Message;

use Exception;
use Marvic;
use Marvic\HTTP\Header\Collection as Headers;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Request\Methods;
use Marvic\HTTP\Message\Response;

/**
 * The HTTP Message Transport (either request or response).
 *
 * @package Marvic\HTTP\Message
 */
final class Transport {
	/**
	 * Send the request to an external and return a response.
	 *
	 * @param  Marvic\HTTP\Message\Request  $request
	 * @return Marvic\HTTP\Message\Response
	 */
	public function sendRequest(Request $request): Response {
		$host = $request->hostname;
		$port = $request->port;
		
		if ($port === 443) $host = 'ssl://' . $host;

		$socket = fsockopen($host, $port, $errno, $errstr);
		if (! $socket ) throw new Exception("Socket Error $errno - $errstr\r\n");

		if ( fwrite($socket, "$request") === false ) {
			fclose($socket);
			$message  = 'Error to send the request: ';
			$message .= "$request->method $request->url $request->version";
			throw new Exception($message);
		}

		$output = '';
		while (! feof($socket) ) $output .= fgets($socket, 128);
		fclose($socket);

		if ( empty($output) ) {
			$message = "Error to get the response: ";
			$message .= "$request->method $request->url";
			throw new Exception($message);
		}

		[$headers, $body] = explode("\r\n\r\n", $output);
		$headers = explode("\r\n", $headers);

		[$version, $status, $phrase] = explode(' ', array_shift($headers));

		$_headers = new Headers();
		foreach ($headers as $index => $header) {
			if ( strpos($header, ': ') === false ) continue;
			[$key, $value] = explode(': ', $header);
			$_headers->set($key, $value);
		}

		return new Response($request, $status, [
			'headers' => $_headers,
			'body'    => $body,
		]);
	}

	/**
	 * Send a response to the client browser.
	 *
	 * @param Marvic\HTTP\Message\Response $response
	 */
	public function sendResponse(Response $response): void {
		$request = $response->request;
		$app     = $request->app;

		if ( $request->fresh ) $response->setStatus(304);

		if ( $app->settings->is('http.xPoweredBy', true) )
			$response->headers->set('X-Powered-By', 'Marvic '. Marvic::VERSION);

		if ( in_array($response->status, [204, 205, 303, 304, 307, 308]) ) {
			$response->headers->remove('Content-Type');
			$response->headers->remove('Content-Length');
			$response->headers->remove('Transfer-Encoding');
			$response->write('');
		}
		if ( $response->status === 205 ) {
			$response->headers->set('Content-Length', 0);
		}
		if ( $response->status === 204 ) {
			$response->setType('text/html', 'UTF-8');
			$response->write( Status::phrase(204) );
		}

		if (! $response->headers->has('Date') ) {
			$response->headers->get('Date', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		if (! $response->headers->has('Cache-Control') ) {
			$response->headers->get('Cache-Control', 'no-store, no-cache, must-revalidate');
		}

		$origin = $request->headers->get('Origin', '');
		$allowedOrigins = $app->get('http.alloewdOrigins', []);
		if ( in_array($origin, $allowedOrigins) ) {
			$response->headers->set('Access-Control-Allow-Origin', $origin);
		}

		if ( $request->headers->has('Access-Control-Request-Methods') ) {
			$allowedMethods = implode(', ', $app->get('http.allowedMethods'));
			$response->headers->set('Access-Control-Allow-Methods', $allowedMethods);
		}

		if ( $request->headers->has('Access-Control-Request-Headers') ) {
			$allowedHeaders = $app->get('http.allowedHeaders', []);
			$requestHeaders = $request->headers->get('Access-Control-Request-Headers');
			$requestHeaders = array_map('trim', explode(', ', $requestHeaders));
			$intersection = array_intersect($allowedHeaders, $requestHeaders);

			if (count($intersection) === count($requestHeaders)) {
				$allowedHeaders = implode(', ', $allowedHeaders);
				$response->headers->set('Access-Control-Allow-Headers', $allowedHeaders);
			}
		}

		header("$response->version $response->status $response->phrase");
		$response->headers->send();
		$response->cookies->send();
		$request->session->send();

		if ($response->status === 206 && $response->headers->has('Content-Range')) {
			$rangeStr = $response->headers->get('Content-Range');
			preg_match('/bytes (\d+)-(\d+)\/(\d+)/', $rangeStr, $found);

			$start = intval( $found[1] );
			$end   = intval( $found[2] );
			$size  = intval( $found[3] );

			$_file = fopen($response->body, 'rb');
			fseek($_file, $start);

			while ( !feof($_file) && ftell($_file) <= $end )
				fread($_file, 1024 * 8); flush();
			fclose($_file);
	
			return;
		}
		if ( $response->headers->has('Content-Disposition') ) {
			readfile($response->read()); return;
		}

		echo $response->body;
	}
}
