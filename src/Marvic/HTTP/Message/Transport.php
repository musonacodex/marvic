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
			readfile($response->body); return;
		}

		echo $response->body;
	}
}
