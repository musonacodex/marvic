<?php

namespace Marvic\HTTP\Message;

use Exception;

use Marvic\Application;
use Marvic\Routing\Route;
use Marvic\HTTP\MimeTypes;
use Marvic\HTTP\Session;
use Marvic\HTTP\Message;
use Marvic\HTTP\Message\Request\Url;
use Marvic\HTTP\Message\Request\Methods;
use Marvic\HTTP\Header\Collection as Headers;
use Marvic\HTTP\Cookie\Collection as Cookies;

/**
 * An HTTP Request Representation.
 * 
 * @package Marvic\HTTP
 * @version 1.0.0
 */
final class Request extends Message {
	/** @var Marvic\Routing\Route */
	public ?Route $route = null;

	/** @var Marvic\Core\Application */
	public readonly ?Application $app;

	/** @var string */
	public readonly string $method;

	/** @var Marvic\HTTP\Url */
	public readonly Url $url;

	/** @var string */
	public readonly string $ip;

	/** @var array */
	public readonly array $ips;

	/** @var array */
	private array $input = [];

	/** @var array */
	public readonly ?Session $session;

	/** @var boolean */
	public readonly bool $isxhr;

	/** @var boolean */
	public readonly bool $fresh;

	/** @var boolean */
	public readonly bool $idempotent;

	/** @var array */
	public readonly ?array $types;

	/** @var array */
	public readonly ?array $charsets;

	/** @var array */
	public readonly ?array $languages;

	/** @var array */
	public readonly ?array $encodings;

	/** @var string */
	public readonly ?string $userAgent;

	/** @var string */
	public readonly ?string $connection;

	/**
	 * The Instance Constructor Method.
	 */
	public function __construct(?Application $app, string $method,
		Url $url, array $options = [])
	{
		$this->app     = $app;
		$this->method  = $this->checkMethod($method);
		$this->url     = $url;
		$this->ips     = $options['ips']     ?? [];
		$this->ip      = $this->ips[0]       ?? '';
		$this->input   = $options['input']   ?? [];
		$this->session = $options['session'] ?? null;

		parent::__construct($options['version'], $options['headers'], 
			$options['cookies'], $options['body']);

		$this->fresh = false;
		$this->isxhr = $this->headers->is('X-Requested-With', 'xmlhttprequest');
		
		$this->types      = $this->parseAcceptHeader();
		$this->charsets   = $this->parseAcceptHeader('Charset');
		$this->languages  = $this->parseAcceptHeader('Language');
		$this->encodings  = $this->parseAcceptHeader('Encoding');
		$this->userAgent  = $this->headers->get('User-Agent');
		$this->connection = $this->headers->get('Connection');
		$this->idempotent = Methods::idempotent($method);
	}

	public function __get(string $name): mixed {
		if ($name === 'url') return "$request->url";

		if ( property_exists($this->url, $name))
			return $this->url->$name ?? $this->$name;
		
		if ( !is_null($this->input($name)) )
			return $this->input($name);

		throw new Exception("Inexistent property: $name");
	}

	public function __toString(): string {
		$path = $this->url->fullpath();
		$output  = "$this->method $path $this->version\r\n";
		$output .= "$this->headers\r\n\r\n$this->body";
		return $output;
	}

	private function checkMethod(string $method): string {
		if ( Methods::has($method) ) return $method;
		throw new Exception("Invalid HTTP request method: '$method'");
	}

	private function parseAcceptHeader(string $header = ''): array {
		$values  = [];
		$header  = empty($header) ? 'Accept' : "Accept-$header";
		$accepts = $this->headers->get($header);

		if ( is_null($accepts) ) return [];

		foreach (explode(',', $accepts) as $part) {
			$params = explode(';', $part);
			$value  = trim(array_shift($params));
			$quality = 1.0;

			foreach ($params as $param) {
				if ( strpos('=', $param) !== false ) return [];
				[$key, $val] = explode('=', trim($param, 2));
				if ($key === 'q') $quality = (float) $val;
			}
			array_push($values, ['value'=>$value,'quality'=>$quality]);
		}
		usort($values, function($a, $b) {
			return $a['quality'] <=> $b['quality'];
		});
		return $values;
	}

	public function accepts(string|array ...$types): ?array {
		$acceptable = [];
		$mimetype   = '';
		foreach ($types as $type) {
			if ( is_array($type) ) {
				$acceptable += $this->accepts(...$type); continue;
			}
			if (! MimeTypes::has($type) )
				continue;

			$mimetype = MimeTypes::hasExtension($type)
				? MimeTypes::mimetype($type) : $type;

			if (! in_array($mimetype, array_column($this->types, 'value')) )
				continue;

			foreach ($this->types as $mime) {
				if ($mime['quality'] === 0.0) continue;
				if ($mime['value'] !== $mimetype) continue;

				$flag = $mime['value'] === $mimetype;
				$flag = $flag || $mime['value'] === '*/*';
				$flag = $flag || $mimetype === '*/*';
				$flag = $flag || fnmatch($mime['value'], $mimetype);
				$flag = $flag || fnmatch($mimetype, $mime['value']);
				
				if (! $flag ) continue;
				$acceptable[] = $type;
				break;
			}
		}
		return empty($acceptable) ? null : $acceptable;
	}

	public function acceptsCharsets(string|array ...$charsets): ?array {
		$acceptable = [];
		foreach ($charsets as $charset) {
			if ( is_array($charset) ) {
				$acceptable += $this->accepts(...$charset); continue;
			}
			$options = array_column($this->charsets, 'value');
			if (! in_array($charset, $options) ) continue;

			$acceptable[] = $charset;
		}
		return empty($acceptable) ? null : $acceptable;
	}

	public function acceptsLanguages(string|array ...$languages): ?array {
		$acceptable = [];
		foreach ($languages as $language) {
			if ( is_array($language) ) {
				$acceptable += $this->accepts(...$language); continue;
			}
			$options = array_column($this->languages, 'value');
			if (! in_array($language, $options) ) continue;

			$acceptable[] = $language;
		}
		return empty($acceptable) ? null : $acceptable;
	}

	public function acceptsEncodings(string|array ...$encodings): ?array {
		$acceptable = [];
		foreach ($encodings as $encoding) {
			if ( is_array($encoding) ) {
				$acceptable += $this->accepts(...$encoding); continue;
			}
			$options = array_column($this->encodings, 'value');
			if (! in_array($encoding, $options) ) continue;

			$acceptable[] = $encoding;
		}
		return empty($acceptable) ? null : $acceptable;
	}

	public function query(string $key, mixed $default = null): mixed {
		parse_str($this->query, $query);
		return $query[$key] ?? $default;
	}

	public function input(string $key, mixed $default = null) {
		$data = $this->input + $this->route->extract($this->path);
		return $data[$key] ?? $default;
	}

	public function json(): array {
		if ( $this->type !== 'application/json' ) return [];
		$json = json_decode($this->body, true);

		if ( json_last_error() ) {
			$message = "Bad request: invalid JSON in request body";
			throw new Exception($message);
		}
		return $json;
	}
}