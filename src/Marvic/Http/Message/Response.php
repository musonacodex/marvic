<?php 

namespace Marvic\Http\Message;

use RuntimeException;
use InvalidArgumentException;
use Marvic\Http\Message;
use Marvic\Http\Message\Request;
use Marvic\Http\Message\Response\Status;
use Marvic\Http\Message\Header\Collection as Headers;
use Marvic\Http\Message\Cookie\Collection as Cookies;

final class Response extends Message {
	public readonly Request $request;

	private int    $status;
	private string $phrase;

	private int    $length;
	private string $type;
	private string $charset;

	private bool $ended = false;

	public function __construct(Request $request, int $status = 200) {
		$this->request = $request;
		$this->setStatus($status);

		$version = $request->version;
		$headers = new Headers();
		$cookies = new Cookies();
		parent::__construct($version, $headers, $cookies);

		$this->length  = 0;
		$this->type    = 'text/html';
		$this->charset = 'utf8';
	}

	public function __get(string $name): mixed {
		$allowed = ['status','phrase','type','charset','length','ended'];
		if ( in_array($name, $allowed) ) return $this->$name;
		$message = sprintf('Undefined property: %s::%s', __CLASS__, $name);
		throw new RuntimeException($message);
	}

	public function __toString(): string {
		$cookieHeaders = implode("\r\n", $this->cookies->toHeaders());
		$output  = "HTTP/$this->version $this->status $this->phrase";
		$output .= $this->headers->empty() ? '' : "\r\n$this->headers";
		$output .= $this->cookies->empty() ? '' : "\r\n$cookieHeaders";
		$output .= "\r\n\r\n$this->body";
		return $output;
	}

	private function checkResponse(): void {
		if (! $this->ended ) return;
		$message = "Cannot modify response after it has ended.";
		throw new RuntimeException($message);
	}

	private function validateStatus(int $status): int {
		if ( Status::has($status) ) return $status;
		$message = "Invalid response status: $status";
		throw new InvalidArgumentException($message);
	}

	public function setStatus(int $status): void {
		Status::validateOrFail($status);
		$this->checkResponse();
		$this->status = $status;
		$this->phrase = Status::phrase($status);
	}

	public function setType(string $type, string $charset = ''): void {
		$this->checkResponse();
		$this->type    = $type;
		$this->charset = $charset;
		$value = $this->type . ($charset ? "; charset=$charset" : '');
		$this->set('Content-Type', $value);
	}

	public function remove(string $key): void {
		$this->checkResponse();
		$this->headers->remove($key);
	}

	public function removeCookie(string $key): void {
		$this->checkResponse();
		$this->cookies->remove($key);
	}

	public function set(string $key, string|array $value): void {
		$this->checkResponse();
		$this->headers->set($key, $value);
	}

	public function setCookie(string $key, string|int $value, array $options = []): void {
		$this->checkResponse();
		$request = $this->request;

		if (!isset($options['path'])) {
			$options['path']   = '/';
		}
		if (!isset($options['domain'])) {
			$options['domain'] = $request->host;
		}
		if (!isset($options['maxAge'])) {
			$options['maxAge'] = 3600; // 1 hour
		}
		if (!isset($options['secure'])) {
			$options['secure'] = $request->scheme === 'https';
		}

		$this->cookies->set($key, $value, $options);
	}

	public function write(string $content): void {
		$this->checkResponse();
		$this->body = $content;
		$this->length = strlen($this->body);
		$this->set('Content-Length', $this->length);
	}

	public function append(string $content): void {
		$this->checkResponse();
		$this->body .= $content;
		$this->length = strlen($this->body);
		$this->set('Content-Length', $this->length);
	}

	public function sendStatus(int $status, ?string $content = null): void {
		$this->checkResponse();
		$this->validateOrFail($status);
		$this->setStatus($status);
		$this->write($content ?? $this->phrase);
		$this->end();
	}

	public function sendJson(array $json): void {
		$this->checkResponse();
		$content = json_encode($json);
		$this->body = $content;
		$this->setType('application/json', 'UTF-8');
		$this->end();
	}

	public function redirect(string $path, int $status = 302): void {
		$this->checkResponse();
		$this->setStatus($status);
		$this->set('Location', $path);
		$this->end();
	}

	public function format(array $cases): void {
		$this->checkResponse();
		$request = $this->request;

		foreach ($cases as $key => $callback) {
			if (! $request->accepts($key) ) continue;

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

	public function send(mixed $content): void {
		$this->checkResponse();

		if ( is_int($content) ) {
			$this->sendStatus($content);
		}
		else if ( is_array($content) ) {
			$this->sendJson($content);
		}
		else if ( is_string($content) ) {
			$this->setType('text/html', 'UTF-8');
			$this->write($content);
			$this->end();
		}
		else if ( is_null($content) ) {
			$this->setStatus(Status::NOT_CONTENT);
			$this->write('');
			$this->end();
		} else {
			$message = 'Unsupported argument type: '. gettype($body);
			throw new InvalidArgumentException($message);
		}
	}

	public function end(): void {
		$this->checkResponse();
		$request = $this->request;

		if (! Status::allowsBody($this->status) ) {
			$this->write('');
			$this->remove('Content-Type');
			$this->remove('Content-Length');
			$this->remove('Transfer-Encoding');
		}
		if ( $this->status === Status::NO_CONTENT ) {
			$this->setType('text/html', 'UTF-8');
			$this->write( Status::phrase(Status::NO_CONTENT) );
		}
		if ( $this->status === Status::RESET_CONTENT ) {
			$this->set('Content-Length', 0);
			$this->remove('Transfer-Encoding');
		}

		if (! $this->has('Host') ) {
			$this->set('Host', $request->authority);
		}
		if (! $this->has('Connection') ) {
			$this->set('Connection', 'close');
		}
		if (! $this->has('Date') ) {
			$this->set('Date', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		if (! $this->has('Cache-Control') ) {
			$this->set('Cache-Control', ['no-store','no-cache','must-revalidate']);
		}

		foreach ($request->allCookies() as $key => $value) {
			if (! $this->hasCookie($key) ) $this->setCookie($key, $value);
		}

		$this->ended = true;
	}
}
