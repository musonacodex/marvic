<?php

namespace Marvic\HTTP\Message;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Marvic\HTTP\MimeTypes;
use Marvic\HTTP\Message;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Request\Url;
use Marvic\HTTP\Message\Response\Status;
use Marvic\HTTP\Header\Collection as Headers;
use Marvic\HTTP\Cookie\Collection as Cookies;

/**
 * HTTP Response
 *
 * This class provide helper functions for different cases to prepare
 * the HTTP response data before to send to the browser, such as HTML,
 * JSON, Files, Streaming, View Rendering and Redirection.
 * 
 * @package Marvic\HTTP\Message
 */
final class Response extends Message {
	/**
	 * HTTP Request that will be respond.
	 *  
	 * @var Marvic\HTTP\Message\Request
	 */
	public readonly Request $request;

	/**
	 * HTTP Response Status Code. 
	 * 
	 * @var integer
	 */
	private int $status;

	/**
	 * HTTP Response Status Phrase
	 * 
	 * @var string
	 */
	private string $phrase;
	
	/**
	 * Is the HTTP Response ready to be sent? 
	 * 
	 * @var boolean
	 */
	private bool $ended = false;

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param Request $request
	 * @param integer $status
	 * @param array   $options
	 */
	public function __construct(Request $request, int $status = 200,
		array $options = [])
	{
		$this->request = $request;
		$this->setStatus($status);

		parent::__construct(
			$this->request->version, 
			$options['headers'] ?? new Headers(), 
			$options['cookies'] ?? clone $request->cookies, 
			$options['body']    ?? ''
		);
		if ( is_null($this->request->app) ) $this->end();
	}

	/**
	 * Get private properties as readonly.
	 * 
	 * @param  string $name
	 * @return mixed
	 */
	public function __get(string $name): mixed {
		return $this->$name;
	}

	/**
	 * Build and return the raw HTTP response representation.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		$output = [];
		$output[] = "$this->version $this->status $this->phrase";
		$output[] = "$this->headers";
		$output[] = $this->cookies->toString(false);

		$output = implode("\r\n", $output);
		$output = implode("\r\n\r\n", [$output, $this->read()]);
		return $output;
	}
	
	/**
	 * Check if the HTTP response is ended.
	 */
	private function checkResponse(): void {
		if (! $this->ended ) return;
		$message = "Cannot modify response after it has ended.";
		throw new RuntimeException($message);
	}

	/**
	 * Set the HTTP response status.
	 * 
	 * @param int $code
	 */
	public function setStatus(int $code): void {
		if ( !Status::has($code) )
			throw new Exception("Inexistent HTTP status code: $code");
		$this->status = $code;
		$this->phrase = Status::phrase($code);
	}

	/**
	 * Set the content type of HTTP response.
	 * 
	 * @param string      $type
	 * @param string|null $charset
	 */
	public function setType(string $type, ?string $charset = null): void {
		$this->type    = $type;
		$this->charset = $charset ?? $this->charset;

		$this->headers->set('Content-Type', $type . (
			is_null($charset) ? '' : "; charset=$charset"
		));
	}
	
	/**
	 * Write the HTTP response body.
	 * 
	 * @param  string $content
	 */
	public function write(string $content): void {
		$this->checkResponse();
		$this->body   = $content;
		$this->length = strlen($this->read());
	}
	
	/**
	 * Append the HTTP response body.
	 * 
	 * @param string $content
	 */
	public function append(string $content): void {
		$this->checkResponse();
		$this->body  .= $content;
		$this->length = strlen($this->read());
	}

	/**
	 * Respond to the acceptable formats using an array of mime type
	 * callback cases, from the acceptable mime types of the request
	 * that will be respond.
	 * 
	 * @param array $cases
	 */
	public function format(array $cases): void {
		$this->checkResponse();
		$request = $this->request;

		foreach ($cases as $key => $callback) {
			if ( is_null($request->accepts($key)) ) continue;

			if ( str_starts_with($key, 'text/') )
				$this->setType($key, 'UTF-8');
			else
				$this->setType($key);

			call_user_func_array($cases[$key], []);
			return;
		}
		if ( array_key_exists('default', $cases) ) {
			call_user_func_array($cases['default'], []);
			return;
		}
		$this->setStatus(406);
		$this->setType('text/html', 'UTF-8');
	}

	/**
	 * Send response status with an optional message as response body.
	 * 
	 * @param integer     $status
	 * @param string|null $message
	 */
	public function sendStatus(int $status, ?string $message = null): void {
		$this->checkResponse();

		$this->setStatus($status);
		$this->setType('text/html', 'UTF-8');
		$this->write($message ?? Status::phrase($status) ?? "$status");

		$this->end();
	}

	/**
	 * Send JSON response.
	 * 
	 * @param array $data
	 */
	public function sendJson(array $data = []): void {
		$this->checkResponse();
		try {
			$content = json_encode($data, JSON_THROW_ON_ERROR);
		} catch (Exception $e) {
			$message  = "Error to send json response: ";
			$message .= json_last_error_msg();
			throw new InvalidArgumentException($message);
		}
		$this->setStatus(200);
		$this->setType('application/json', 'UTF-8');
		$this->write($content);
		$this->end();
	}

	/**
	 * Send the HTTP response.
	 * 
	 * @param array $data
	 */
	public function render(string $view, array $data = []): void {
		$this->checkResponse();
		$app       = $this->request->app;
		$directory = $app->get('folders.views');

		$this->setType('text/html', 'UTF-8');
		$this->write( $app->render($view, $data) );
		$this->end();
	}

	/**
	 * Redirect to the given URL with optional response status.
	 * 
	 * @param  string  $url
	 * @param  integer $status
	 */
	public function redirect(string $url, int $status = 302): void {
		$this->checkResponse();
		$this->setStatus($status ?? 302);
		$this->headers->set('Location', $url);
		$this->end();
	}

	/**
	 * Transfer a file from a given 'path'.
	 * 
	 * @param  string $filepath
	 * @param  array  $options
	 */
	public function sendFile(string $filepath, array $options = []): void {
		$this->checkResponse();
		$directory = $options['basedir'] ?? '';
		$headers   = $options['headers'] ?? [];
		
		if( $directory ) $filepath = "$directory/$filepath";

		if (!file_exists($filepath) && !is_file($filepath)) {
			$this->setStatus(404);
			return;
		}
		$filename  = basename($filepath);
		$extension = pathinfo($filepath, PATHINFO_EXTENSION);
		$mimetype  = MimeTypes::mimetype($extension);
		
		if ( preg_match('#text/.*#', $mimetype) )
			$mimetype .= '; charset="UTF-8"';

		$this->length = filesize($filepath);
		$this->write($filepath);

		$this->setStatus(200);
		$this->setType($mimetype ?? 'application/octet-stream');
		$this->headers->set('Content-Length', $this->length);
		$this->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
		
		foreach ($headers as $key => $value)
			$this->headers->set($key, $value);

		$this->end();
	}

	/**
	 * Transfer a file from a given 'path' with an attachment.
	 * 
	 * @param  string $filepath
	 * @param  array  $options
	 */
	public function download(string $filepath, array $options = []): void {
		$headers = array_merge([
			'Pragma'              => 'public',
			'Cache-Control'       => 'must-revalidate',
			'Content-Type'        => 'application/octet-stream',
			'Content-Description' => 'File Transfer: ' . basename($filepath),
		], $options['headers'] ?? []);

		$this->sendFile($filepath, $options);
	}

	/**
	 * Stream a file from a given 'path'.
	 * 
	 * @param  string $filepath
	 * @param  array  $options
	 */
	public function stream(string $filepath, array $options = []): void {
		$request   = $this->request;
		$app       = $request->app;
		$directory = $options['basedir'] ?? $app->get('folders.uploads');
		$filepath  =  $directory . $filepath;
		$filename  = basename($filepath);
		$filesize  = filesize($filepath);

		[$begin, $end] = [0, $filesize - 1];

		if ( $request->headers->has('Range') ) {
			$range = $request->headers->get('Range');
			preg_match('/bytes=(\d+)-(\d*)/', $range, $found);

			$begin = intval($found[1]);
			if (! empty($found[2]) ) $end = intval($found[2]);
			
			$this->setStatus(206);
		}
		$partialsize = $end - $begin + 1;

		$headers = [
			'Content-Length' => "$partialsize",
			'Content-Range'  => "bytes $begin-$end/$filesize",
		];

		if ( isset($options['headers']) )
			$options['headers'] += $headers;
		else
			$options['headers'] = $headers;

		$this->download($filepath, $options);
	}

	/**
	 * Send the response. It depends of the given content for response body.
	 * 
	 * @param mixed|null $body
	 */
	public function send(mixed $body = null): void {
		$this->checkResponse();
		
		switch (true) {
			case is_integer($body) && Status::has($body):
				$this->sendStatus($body);
				break;

			case is_string($body) && file_exists($body) && is_file($body):
				$this->sendFile($body);
				break;

			case is_array($body):
				$this->sendJson($body);
				break;

			case is_string($body):
				$this->setStatus(200);
				$this->setType('text/html', 'UTF-8');
				$this->write($body);
				$this->end();
				break;

			case is_null($body):
				$this->setStatus(204);
				$this->setType('text/plain', 'UTF-8');
				$this->write('');
				$this->end();
				break;
				
			default:
				$message = 'Unsupported argument type: '. gettype($body);
				throw new InvalidArgumentException($message);
		}			
	}

	/**
	 * End the changes of HTTP response data.
	 */
	public function end(): void {
		$this->checkResponse();
		$request = $this->request;
		$app     = $request->app;

		if ( $request->fresh ) $this->setStatus(304);

		if ( in_array($this->status, [204, 205, 303, 304, 307, 308]) ) {
			$this->headers->remove('Content-Type');
			$this->headers->remove('Content-Length');
			$this->headers->remove('Transfer-Encoding');
			$this->write('');
		}
		if ( $this->status === 205 ) {
			$this->headers->set('Content-Length', 0);
		}
		if ( $this->status === 204 ) {
			$this->setType('text/html', 'UTF-8');
			$this->write( Status::phrase(204) );
		}

		if (! $this->headers->has('Date') ) {
			$this->headers->get('Date', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		if (! $this->headers->has('Cache-Control') ) {
			$this->headers->get('Cache-Control', 'no-store, no-cache, must-revalidate');
		}

		$origin = $request->headers->get('Origin', '');
		$allowedOrigins = $app->get('http.alloewdOrigins', []);
		if ( in_array($origin, $allowedOrigins) ) {
			$this->headers->set('Access-Control-Allow-Origin', $origin);
		}

		if ( $request->headers->has('Access-Control-Request-Methods') ) {
			$allowedMethods = implode(', ', $app->get('http.allowedMethods'));
			$this->headers->set('Access-Control-Allow-Methods', $allowedMethods);
		}

		if ( $request->headers->has('Access-Control-Request-Headers') ) {
			$allowedHeaders = $app->get('http.allowedHeaders', []);
			$requestHeaders = $request->headers->get('Access-Control-Request-Headers');
			$requestHeaders = array_map('trim', explode(', ', $requestHeaders));
			$intersection = array_intersect($allowedHeaders, $requestHeaders);

			if (count($intersection) === count($requestHeaders)) {
				$allowedHeaders = implode(', ', $allowedHeaders);
				$this->headers->set('Access-Control-Allow-Headers', $allowedHeaders);
			}
		}

		if ( $app->settings->is('http.xPoweredBy', true) )
			$response->headers->set('X-Powered-By', 'Marvic '. Marvic::VERSION);

		$this->ended = true;
	}
}
