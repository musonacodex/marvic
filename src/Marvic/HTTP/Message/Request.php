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
 * HTTP Request
 * 
 * This class parse HTTP request headers and body, avaliable through
 * properies and methods.
 * 
 * @package Marvic\HTTP\Message
 */
final class Request extends Message {
	/** 
	 * The Route that it wiil handle the HTTP Request.
	 * 
	 * @var Marvic\Routing\Route
	 */
	public ?Route $route = null;

	/**
	 * Marvic Application that it issued the HTTP Request..
	 * 
	 * @var Marvic\Core\Application
	 */
	public readonly ?Application $app;

	/** 
	 * The HTTP Request Method.
	 * 
	 * @var string
	 */
	public readonly string $method;

	/** 
	 * The HTTP Request URL.
	 * 
	 * @var Marvic\HTTP\Message\Request\Url
	 */
	public readonly Url $url;

	/** 
	 * The HTTP Request Host as IP address.
	 * 
	 * @var string
	 */
	public readonly string $ip;

	/** 
	 * The HTTP Request IP's (Proxy).
	 * 
	 * @var array
	 */
	public readonly array $ips;

	/** 
	 * The HTTP Request Body Parameters.
	 * 
	 * @var array
	 */
	private array $input = [];

	/** 
	 * The HTTP Sesstion.
	 * 
	 * @var array
	 */
	public readonly ?Session $session;

	/** 
	 * Was the HTTP Request sent through XMLHttpRequest?
	 * 
	 * @var boolean
	 */
	public readonly bool $isxhr;

	/** 
	 * Is the HTTP Request fresh?
	 * 
	 * @var boolean
	 */
	public readonly bool $fresh;

	/** 
	 * Is the HTTP Request Method Idempotent?
	 * 
	 * @var boolean
	 */
	public readonly bool $idempotent;

	/** 
	 * List of all acceptable mime types of HTTP request.
	 * 
	 * @var array|null
	 */
	public readonly ?array $types;

	/** 
	 * List of all acceptable charsets of HTTP request.
	 * 
	 * @var array|null
	 */
	public readonly ?array $charsets;

	/** 
	 * List of all acceptable languages of HTTP request.
	 * 
	 * @var array|null
	 */
	public readonly ?array $languages;

	/** 
	 * List of all acceptable encodings of HTTP request.
	 * 
	 * @var array|null
	 */
	public readonly ?array $encodings;

	/** 
	 * The User-Agent Header Value of HTTP request.
	 * 
	 * @var string|null
	 */
	public readonly ?string $userAgent;

	/** 
	 * The Connection Header Value of HTTP request.
	 * 
	 * @var string|null
	 */
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

	/**
	 * Get an URL property or request input parameter if the
	 * URL property does not exists.
	 * 
	 * @param  string $name
	 * @return mixed
	 */
	public function __get(string $name): mixed {
		if ($name === 'url') return "$request->url";

		if ( property_exists($this->url, $name))
			return $this->url->$name ?? $this->$name;
		
		if ( !is_null($this->input($name)) )
			return $this->input($name);

		throw new Exception("Inexistent property: $name");
	}

	/**
	 * The HTTP Request String Representaion.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		$path = $this->url->fullpath();
		$output  = "$this->method $path $this->version\r\n";
		$output .= "$this->headers\r\n\r\n$this->body";
		return $output;
	}

	/**/
	private function checkMethod(string $method): string {
		if ( Methods::has($method) ) return $method;
		throw new Exception("Invalid HTTP request method: '$method'");
	}

	/**
	 * Internal: parse any 'Accept' header.
	 * 
	 * @param  string $header
	 * @return array
	 */
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

	/**
	 * Select the acceptable mime types by the HTTP Request.
	 * 
	 * @param  array $types
	 * @return array
	 */
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

	/**
	 * Select the acceptable charsets by the HTTP Request.
	 * 
	 * @param  array $charsets
	 * @return array
	 */
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

	/**
	 * Select the acceptable languages by the HTTP Request.
	 * 
	 * @param  array $languages
	 * @return array
	 */
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

	/**
	 * Select the acceptable encodings by the HTTP Request.
	 * 
	 * @param  array $encodings
	 * @return array
	 */
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

	/**
	 * Get a value of an URL query key.
	 * 
	 * @param  string     $key
	 * @param  mixed|null $defaulT
	 * @return mixed
	 */
	public function query(string $key, mixed $default = null): mixed {
		parse_str($this->query, $query);
		return $query[$key] ?? $default;
	}

	/**
	 * Get a value of an HTTP request input data.
	 * 
	 * @param  string     $key
	 * @param  mixed|null $defaulT
	 * @return mixed
	 */
	public function input(string $key, mixed $default = null) {
		$data = $this->input + $this->route->extract($this->path);
		return $data[$key] ?? $default;
	}

	/**
	 * Parse the HTTP request body into JSON.
	 * 
	 * @return array
	 */
	public function json(): array {
		if ( $this->type !== 'application/json' ) return [];
		$json = json_decode($this->body, true);
		if (! json_last_error() ) return $json;

		$message = "Bad request: invalid JSON in request body";
		throw new Exception($message);
	}
}