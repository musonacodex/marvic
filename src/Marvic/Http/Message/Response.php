<?php 

namespace Marvic\Http\Message;

use RuntimeException;
use InvalidArgumentException;
use Marvic\Http\MimeTypes;
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

	private function sanitizePath(string $path): ?string {
		$path = rawurldecode($path);
		$path = str_replace("\0", '', $path);
		$path = str_replace("\\", '/', $path);
		$path = !str_starts_with($path, '/') ? "/$path" : $path;

		if (str_contains($path, '../')) return null;
		return $path;
	}

	private function generateLastModified(string $path): string {
		$lastModified = filemtime($path);
		if ($lastModified === false) return '';
		return gmdate('D, d M Y H:i:s T', $lastModified);
	}

	private function generateEtag(): string {
		$stat = stat($path);
		if ($stat === false) {
			return '"'. md5_file($path) .'"';
		} else {
			$ino   = $stat['ino'];
			$size  = $stat['size'];
			$mtime = $stat['mtime'];
			return "\"$ino-$size-$mtime\"";
		}
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
		Status::validateOrFail($status);
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

	public function render(string $path, array $data = []): void {
		$this->checkResponse();
		$app = $this->request->app;
		$compiled = $app->render($path, $data);
		$this->write($compiled);
		$this->setType('text/html', 'UTF-8');
		$this->end();
	}

	public function redirect(string $path, int $status = 302): void {
		$this->checkResponse();
		$this->setStatus($status);
		$this->set('Location', $path);
		$this->end();
	}

	public function sendFile(string $path, ?string $name = null, array $options = []): void {
		$maxAge            = $options['maxAge']       ?? 3600; // 1 hour
		$basedir           = $options['root']         ?? null;
		$useEtag           = $options['etag']         ?? false;
		$useCache          = $options['cache']        ?? false;
		$dotfiles          = $options['dotfiles']     ?? null;
		$disposition       = $options['disposition']  ?? 'inline';
		$useLastModified   = $options['lastModified'] ?? false;
		$additionalHeaders = $options['headers']      ?? [];

		if ($basedir === null) {
			$app = $request->app;
			$basedir = $app->get('app.folders.uploads', './uploads');
		}

		$file = $this->sanitizePath("$basedir/$path");
		if ( is_null($file) ) {
			$message = "Invalid file path: $basedir/$path";
			throw new InvalidArgumentException($message);
		}
		if (! file_exists($file) ) {
			$message = "File is not found: $file";
			throw new InvalidArgumentException($message);
		}
		if (! is_readable($file) ) {
			$message = "File is not readable: $file";
			throw new InvalidArgumentException($message);
		}

		$this->length = filesize($file);
		$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		$mimetype  = MimeTypes::mimetype($extension, 'application/octet-stream');

		$filename = $name ?? basename($file);
		$filename = preg_replace('/[\x00-\x1f\x7f]/', '', $filename);
		$filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

		$utf8Filename = rawurlencode($filename);
		if ($filename !== $utf8Filename)
			$filename = $utf8Filename;
		$disposition .= "; filename=\"$filename\"";

		if (str_starts_with($filename, '.') && $dotfiles !== true) {
			if ($dotfiles === null) return;
			if ($dotfiles === false) {
				$response->sendStatus(Status::NOT_FOUND);
				return;
			}
		}
		$this->setType($mimetype);
		$this->set('Content-Length', $this->length);
		$this->set('Content-Disposition', $disposition);
		$this->set('X-Content-Type-Options', 'nosniff');

		if ($useCache) {
			$this->set('Cache-Control', "public, max-age=$maxAge, must-revalidate");
			if ($useEtag) {
				$this->set('ETag', $this->generateEtag());
			}
			if ($useLastModified && filemtime($path) !== false) {
				$this->set('Last-Modified', $this->generateLastModified());
			}
			$this->set('Pragma', 'public');
		} else {
			$this->set('Cache-Control', 'no-cache, no-store, must-revalidate');
			$this->set('Pragma', 'no-cache');
			$this->set('Expires', '0');
		}

		foreach ($additionalHeaders as $key => $value) {
			$this->set($key, $value);
		}
		$this->body = $file;
		$this->end();
	}

	public function download(string $path, ?string $name = null, array $options = []): void {
		$options['disposition'] = 'attachment';
		$options['headers'] = array_merge([
			'Content-Type'        => 'application/octet-stream',
			'Content-Description' => 'File Transfer: '. ($name ?? basename($path)),
		], $options['headers'] ?? []);
		$this->sendFile($path, $name, $options);
	}

	public function stream(string $path, ?string $name = null, array $options = []): void {
		$basedir = $options['root'] ?? null;

		if ($basedir === null) {
			$app = $request->app;
			$basedir = $app->get('app.folders.uploads', './uploads');
		}

		$file = $this->sanitizePath("$basedir/$path");
		if ( is_null($file) ) {
			$message = "Invalid file path: $basedir/$path";
			throw new InvalidArgumentException($message);
		}
		if (! file_exists($file) ) {
			$message = "File does not found: $file";
			throw new InvalidArgumentException($message);
		}
		if (! is_readable($file) ) {
			$message = "File is not readable: $file";
			throw new InvalidArgumentException($message);
		}

		$request  = $this->request;
		$filesize = filesize($file);
		[$begin, $end] = [0, $filesize - 1];

		if ($range = $request->get('Range')) {
			preg_match('/bytes=(?<begin>\d+)-?(?<end>\d*)/', $range, $found);
			$begin = intval($found['begin']);
			$end   = empty($found['end']) ?: intval($found['end']);
			$this->setStatus(206);
		}

		$options['headers'] = array_merge([
			'Content-Length' => $end - $begin + 1,
			'Content-Range'  => "bytes $begin-$end/$filesize",
		], $options['headers'] ?? []);

		$this->download($path, $name, $options);
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
		else if ( is_string($content) && file_exists($content) ) {
			$this->sendFile($content);
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
			$message = 'Unsupported argument type: '. gettype($content);
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
