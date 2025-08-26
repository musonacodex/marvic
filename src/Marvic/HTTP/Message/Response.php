<?php

namespace Marvic\HTTP\Message;

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
 * An HTTP Response Representation.
 *
 * @package Marvic\HTTP\Message
 * @version 1.0.0
 */
final class Response extends Message {
	/** @var Marvic\HTTP\Message\Request */
	public readonly Request $request;

	/** @var integer */
	private int $status;

	/** @var string */
	private string $phrase;
	
	/** @var boolean */
	private bool $ended = false;

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
	}

	public function __get(string $name): mixed {
		return $this->$name;
	}

	public function __toString(): string {
		$output  = "$this->version $this->status $this->phrase\r\n";
		$output .= "$this->headers\r\n";
		$output .= $this->cookies->toString(false);
		$output .= "\r\n\r\n$this->body";
		return $output;
	}
	
	private function checkResponse(): void {
		if (! $this->ended ) return;
		throw new RuntimeException("Cannot modify response after it has ended.");
	}
	
	public function end(): void {
		$this->ended = true;
	}

	public function setStatus(int $code): void {
		if ( !Status::has($code) )
			throw new Exception("Inexistent HTTP status code: $code");
		$this->status = $code;
		$this->phrase = Status::phrase($code);
	}

	public function setType(string $type, ?string $charset = null): void {
		$this->type    = $type;
		$this->charset = $charset ?? $this->charset;

		$this->headers->set('Content-Type', $type . (
			is_null($charset) ? '' : "; charset=$charset"
		));
	}
	
	public function write(string $content): void {
		$this->checkResponse();
		$this->body = $content;
	}
	
	public function append(string $content): void {
		$this->checkResponse();
		$this->body .= $content;
	}

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

	public function sendStatus(int $status, ?string $message = null): void {
		$this->checkResponse();

		$this->setStatus($status);
		$this->setType('text/html', 'UTF-8');
		$this->write($message ?? Status::phrase($status) ?? "$status");

		$this->end();
	}

	public function sendJson(array $data = []): void {
		$this->checkResponse();

		$content = json_encode($data);
		$this->setStatus(200);
		$this->setType('application/json', 'UTF-8');
		$this->headers->set('Content-Length', strlen($content));
		$this->write( $content );
		$this->end();
	}

	public function render(string $view, array $data = []): void {
		$this->checkResponse();
		$body      = '';
		$app       = $this->request->app;
		$engine    = $app->get('engines.view', null);
		$directory = $app->get('folders.views');
		$data['context'] = $app->context;
		
		$file = "$directory/$view";
		if (! (file_exists($file) && is_file($file)) ) {
			$message = "Inexistent view template file: $file";
			throw new InvalidArgumentException($message);
		}

		if (! is_null($engine) ) {
			$body = $engine->render($view, $data);
		}
		else if ( pathinfo($file, PATHINFO_EXTENSION) !== 'php' ) {
			$body = file_get_contents($file);
		}
		else {
			$body = (function($file, $data, $directory) {
				$oldPaths = get_include_path();
				set_include_path($directory);
				extract($data);
				ob_start();
				include $file;
				$output = ob_get_clean();
				set_include_path($oldPaths);
				return $output;
			})($file, $data, $directory);			
		}

		$this->setType('text/html', 'UTF-8');
		$this->headers->set('Content-Length', strlen($body));
		$this->write($body);
		$this->end();
	}

	public function redirect(string $url, int $status = 302): void {
		$this->checkResponse();
		$this->setStatus($status);
		$this->headers->set('Location', $url);
		$this->end();
	}

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

		$this->setStatus(200);
		$this->setType($mimetype ?? 'application/octet-stream');
		$this->headers->set('Content-Length', filesize($filepath));
		$this->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
		
		foreach ($headers as $key => $value)
			$this->headers->set($key, $value);
		
		$this->write($filepath);
		$this->end();
	}

	public function download(string $filepath, array $options = []): void {
		$headers = array_merge([
			'Pragma'              => 'public',
			'Cache-Control'       => 'must-revalidate',
			'Content-Type'        => 'application/octet-stream',
			'Content-Description' => 'File Transfer: ' . basename($filepath),
		], $options['headers'] ?? []);

		$this->sendFile($filepath, $options);
	}

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
				$this->headers->set('Content-Length', strlen($body));
				$this->write($body);
				$this->end();
				break;

			case is_null($body):
				$this->setStatus(204);
				$this->setType('text/plain', 'UTF-8');
				$this->headers->set('Content-Length', 0);
				$this->write('');
				$this->end();
				break;
				
			default:
				$message = 'Unsupported argument type: '. gettype($body);
				throw new InvalidArgumentException($message);
		}			
	}
}