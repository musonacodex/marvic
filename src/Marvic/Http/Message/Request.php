<?php

namespace Marvic\Http\Message;

use RuntimeException;
use InvalidArgumentException;
use Marvic\Http\Route\Layer;
use Marvic\Http\Message;
use Marvic\Http\Message\Request\Uri;
use Marvic\Http\Message\Request\File;
use Marvic\Http\Message\Request\Methods;
use Marvic\Http\Message\Header\Collection as Headers;
use Marvic\Http\Message\Cookie\Collection as Cookies;

final class Request extends Message {
	public readonly Application $app;

	public readonly string  $method;
	public readonly bool    $safe;
	public readonly bool    $idempotent;
	public readonly bool    $cacheable;
	
	public readonly string $ip;
	public readonly string $type;
	public readonly string $charset;

	public readonly array $ips;
	public readonly array $types;
	public readonly array $charsets;
	public readonly array $encodings;
	public readonly array $languages;

	private readonly Uri $uri;
	private array $parsedBody;

	private string $route  = '/';
	private array  $params = [];
	
	public function __construct(Application $app, string $method,
		Uri $uri, array $options = []) {
		$version = $options['version'] ?? '1.1';
		$headers = $options['headers'] ?? new Headers();
		$cookies = $options['cookies'] ?? new Cookies();
		$body    = $options['body']    ?? '';
		parent::__construct($version, $headers, $cookies, $body);
		
		$this->app        = $app;
		$this->method     = $this->validateMethod($method);
		$this->safe       = Methods::safe($method);
		$this->idempotent = Methods::idempotent($method);
		$this->cacheable  = Methods::cacheable($method);
		
		$this->uri        = $uri;
		$this->parsedBody = $options['parsedBody'] ?? [];

		$this->ips = $this->getClientIPChain();
		$this->ip  = $this->ips[0];

		$this->types     = $this->parseAcceptHeader('Accept');
		$this->charsets  = $this->parseAcceptHeader('Accept-Charset');
		$this->encodings = $this->parseAcceptHeader('Accept-Encoding');
		$this->languages = $this->parseAcceptHeader('Accept-Language');
		
		$this->detectContentType();
	}
	
	public function __toString(): string {
		$output  = "$this->method {$this->uri->fullpath} HTTP/$this->version";
		$output .= $this->headers->empty() ? '' : "\r\n$this->headers";
		$output .= $this->cookies->empty() ? '' : "\r\nCookie: $this->cookies";
		$output .= empty($this->body)      ? '' : "\r\n\r\n$this->body";
		return $output;
	}
	
	public function __isset(string $name): bool {
		return isset($this->uri->$name);
	}

	public function __get(string $name): mixed {
		$allowed = ['route', 'params'];
		if ( in_array($name, $allowed) ) return $this->{$name};

		if ($name === 'uri') return $this->uri->fullurl;
		if (property_exists($this->uri, $name)) return $this->uri->$name;

		$trace = debug_backtrace();
		$message = sprintf('Undefined property: %s::%s', __CLASS__, $name);
		trigger_error($message,	E_USER_NOTICE);
		return null;
	}
	
	private function validateMethod(string $method): string {
		if ( Methods::has($method) ) return $method;
		$message = "Invalid request method: {$method}";
		throw new InvalidArgumentException($message);
	}
		
	private function getClientIPChain(): array {
		$ips = [];
		$headers = ['X-Forwarded-For', 'X-Forwarded', 'Forwarded-For',
			'Forwarded', 'Client-IP', 'X-Real-IP', 'X-Client-IP',
			'X-Cluster-Client-IP', 'CF-Connecting-IP', 'True-Client-IP',];

		foreach ($headers as $header) {
			if (! $this->has($header) ) continue;
			$value = $this->headers->getValue($header, '');
			if ($value === '') continue;
        
			$items = array_map('trim', explode(',', $value));
			foreach ($items as $item) {
				if (! filter_var($item, FILTER_VALIDATE_IP) ) continue;
				if (empty($ips) || end($ips) !== $item) $ips[] = $item;
			}
		}
		return empty($ips) ? ['0.0.0.0'] : $ips;
	}
	
	private function detectContentType(): void {
		$contentType = $this->get('Content-Type', null);
		if ($contentType === null || $this->parsedBody !== []) return;

		$params = '';
		foreach (explode(';', $contentType) as $index => $item) {
			if ($index === 0) {
				$this->type = $item;
			}
			else if (str_contains($item, '=')) {
				[$key, $value] = explode('=', $item, 2);
				$params[trim($key)] = trim($value);
			}
		}
		$this->charset = $params['charset'] ?? '';

		if ($this->type === 'application/json') {
			$this->parsedBody = json_decode($this->body, true) ?? [];
		}
		else if ($this->type === 'application/x-www-form-urlencoded') {
			parse_str($this->body, $this->parsedBody);
		}
	}

	private function parseAcceptHeader(string $header): array {
		$acceptedList = $this->get($header, null);
		if ($acceptedList === null) return [];
		$parts = array_map('trim', explode(',', $acceptedList));
		
		$items = [];
		foreach ($parts as $value) {
			if ($value === '') continue;
			
			$quality = 1.0;
			if (str_contains($value, ';q=')) {
				[$value, $q] = explode(';q=', $value);
				$quality = (float) $q;
				$value = trim($value);
			}
			if (!isset($items[$value]) || $quality > $items[$value]['quality']) {
				$items[$value] = ['value' => $value, 'quality' => $quality];
			}
		}
		
		usort($items, function($a, $b) {
			return $b['quality'] <=> $a['quality'];
		});
		
		return $items;
	}
    
	private function acceptGeneric(array $acceptedList, array $options): array {
		if ( empty($options) ) return null;

		$mimeMatches = function(string $acceptable, string $requested): bool {
			if (! str_ends_with($acceptable, '/*') ) return false;
			$prefix = substr($acceptable, 0, -2);
			return str_starts_with($requested, $prefix);
		};

		$acceptable = [];
		foreach ($options as $option) {
			if ( is_array($option) ) {
				$result = $this->acceptGeneric($acceptedList, $option);
				if (! is_null($result) ) $acceptable = array_merge($acceptable, $result);
				continue;
			}
            foreach ($acceptedList as $item) {
            	if ($item['quality'] <= 0.0) continue;

            	$itemValue = $item['value'];
            	$optionValue = (string) $option;

            	$flag = $optionValue === '*';
            	$flag = $flag || $itemValue === '*';
            	$flag = $flag || $itemValue === '*/*';
            	$flag = $flag || $itemValue === $optionValue;
            	$flag = $flag || fnmatch($itemValue, $optionValue);
            	$flag = $flag || fnmatch($optionValue, $itemValue);
            	$flag = $flag || $mimeMatches($optionValue, $itemValue);
            	$flag = $flag || $mimeMatches($itemValue, $optionValue);

            	if ($flag) { $acceptable[] = $option; break; }
            }
        }
        return empty($acceptable) ? [] : array_values(array_unique($acceptable));
    }

	public function accepts(string|array ...$types): array {
		return $this->acceptGeneric($this->types, $types);
	}

	public function acceptsCharsets(string|array ...$charsets): array {
		return $this->acceptGeneric($this->charsets, $charsets);
	}

	public function acceptsEncodings(string|array ...$encodings): array {
		return $this->acceptGeneric($this->encodings, $encodings);
	}

	public function acceptsLanguages(string|array ...$languages): array {
		return $this->acceptGeneric($this->languages, $languages);
	}

	public function input(string $key, mixed $default = null): mixed {
		return $this->parsedBody[$key] ?? $default;
	}

	public function params(string $key, mixed $default = null): mixed {
		return $this->params[$key] ?? $default;
	}

	public function applyRoute(Layer $layer, bool $merge = false): void {
		$this->route = $layer->matcher->pattern;
		$parameters = $layer->matcher->match($this->uri->fullurl);
		$this->params = array_merge($this->params, $merge ? $parameters : []);
	}

	public static function fromGlobals(Application $app): self {
		$method = $_SERVER['REQUEST_METHOD'];
		if (isset($_POST) && array_key_exists('__method__', $_POST))
			$method = $_POST['__method__'];

		$headers = Headers::fromGlobals();
		if (! $headers->has('X-Client-IP') )
			$headers->set('X-Client-IP', $_SERVER['REMOTE_ADDR']);

		return new self($app, $method, Uri::fromGlobals(), [
			'version'    => str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']),
			'headers'    => $headers,
			'cookies'    => Cookies::fromGlobals(),
			'body'       => file_get_contents('php://input'),
			'parsedBody' => array_merge($_POST ?? [], File::fromGlobals()),
		]);
	}
}
