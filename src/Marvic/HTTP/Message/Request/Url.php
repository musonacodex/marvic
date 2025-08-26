<?php

namespace Marvic\HTTP\Message\Request;

use Exception;

/**
 * The HTTP Url (Uniform Resource Locator) Representation.
 * 
 * @package Marvic\HTTP
 * @version 1.0.0
 */
final class Url {
	/** @var string */
	private string $location;

	/** @var string */
	public readonly string $protocol;

	/** @var string */
	public readonly string $host;

	/** @var string */
	public readonly string $hostname;

	/** @var integer */
	public readonly int $port;

	/** @var string */
	public readonly ?string $username;

	/** @var string */
	public readonly ?string $password;

	/** @var string */
	public readonly string $path;

	/** @var string */
	public readonly string $query;

	/** @var string */
	public readonly string $fragment;


	public function __construct(string $location) {
		$this->location = $location;
		$this->sanitize();
		$this->validate();
		$this->parse();
	}

	public function __toString(): string {
		return $this->location;
	}

	private function sanitize(): void {
		$this->location = filter_var(trim("$this"), FILTER_SANITIZE_URL);
	}

	private function validate(): void {
		if ( !filter_var(trim("$this"), FILTER_VALIDATE_URL) )
			throw new Exception("Invalid HTTP Url: '$this'");
	}

	private function parse(): void {
		$parsed = parse_url($this->location);

		$this->protocol = $parsed['scheme'];
		$this->hostname = $parsed['host'];
		$this->port     = $parsed['port']     ?? ($this->safe() ? 443 : 80);
		$this->host     = "$this->hostname:$this->port";
		$this->username = $parsed['user']     ?? null;
		$this->password = $parsed['password'] ?? null;
		$this->path     = $parsed['path']     ?? '/';
		$this->query    = $parsed['query']    ?? '';
		$this->fragment = $parsed['fragment'] ?? '';
	}

	public function safe(): bool {
		return $this->protocol === 'https';
	}

	public function fullpath(): string {
		$fullpath = $this->path;
		if ( $this->query )    $fullpath .= "?$this->query";
		if ( $this->fragment ) $fullpath .= "#$this->fragment";
		return $fullpath;
	}

	public function query(string $key, mixed $default = null): mixed {
		parse_str($this->query, $query);
		return $query[$key] ?? $default;
	}
}