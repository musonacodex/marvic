<?php

namespace Marvic\Http;

use InvalidArgumentException;
use Marvic\Http\Message\Header\Collection as Headers;
use Marvic\Http\Message\Cookie\Collection as Cookies;

abstract class Message {
	public    readonly string  $version;
	protected readonly Headers $headers;
	protected readonly Cookies $cookies;
	protected string $body;
	
	public function __construct(string $version, Headers $headers,
		Cookies $cookies, string $body = '')
	{
		$this->version = $this->checkProtocolVersion($version);
		$this->headers = $headers;
		$this->cookies = $cookies;
		$this->body    = $body;
	}

	abstract public function __toString(): string;

	private function checkProtocolVersion(string $version): string {
		if (preg_match('/^(1\.[01]|2(\.0)?|3)$/', $version)) return $version;
		$message = "Invalid HTTP protocol version: {$version}";
		throw new InvalidArgumentException($message);
	}
	
	public function all(): array {
		return $this->headers->toArray();
	}
	
	public function has(string $name): bool {
		return $this->headers->has($name);
	}
	
	public function get(string $name, ?string $default = null): ?string {
		$values = $this->headers->getValues($name);
		return empty($values) ? $default : implode(', ', $values); 
	}

	public function allCookies(): array {
		return $this->cookies->toArray();
	}
	
	public function hasCookie(string $name): bool {
		return $this->cookies->has($name);
	}
	
	public function getCookie(string $name, ?string $default = null): ?string {
		return $this->cookies->getValue($name, $default);
	}

	public function read(): string {
		return $this->body;
	}
}
