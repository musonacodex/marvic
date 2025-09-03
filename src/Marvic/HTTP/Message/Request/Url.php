<?php

namespace Marvic\HTTP\Message\Request;

use Exception;

/**
 * The HTTP Url Parser.
 * 
 * @package Marvic\HTTP\Message\Request
 */
final class Url {
	/** 
	 * The full URL.
	 * 
	 * @var string
	 */
	private string $location;

	/** 
	 * The URL protocol.
	 * 
	 * @var string
	 */
	public readonly string $protocol;

	/** 
	 * The URL hostname or IP address.
	 * 
	 * @var string
	 */
	public readonly string $host;

	/** 
	 * The URL hostname.
	 * 
	 * @var string
	 */
	public readonly string $hostname;

	/** 
	 * The URL port.
	 * 
	 * @var string
	 */
	public readonly int $port;

	/** 
	 * The URL username.
	 * 
	 * @var string
	 */
	public readonly ?string $username;

	/** 
	 * The URL password.
	 * 
	 * @var string
	 */
	public readonly ?string $password;

	/** 
	 * The URL path.
	 * 
	 * @var string
	 */
	public readonly string $path;

	/** 
	 * The URL query.
	 * 
	 * @var string
	 */
	public readonly string $query;

	/** 
	 * The URL fragment.
	 * 
	 * @var string
	 */
	public readonly string $fragment;


	/**
	 * The Instance Constructor Method.
	 * 
	 * @param string $location
	 */
	public function __construct(string $location) {
		$this->location = $location;
		$this->sanitize();
		$this->validate();
		$this->parse();
	}

	/**
	 * The Instance String Representation.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		return $this->location;
	}

	/**
	 * Internal: Sanitize the full URL before to initialize the property.
	 */
	private function sanitize(): void {
		$this->location = filter_var(trim("$this"), FILTER_SANITIZE_URL);
	}

	/**
	 * Internal: Validade the full URL.
	 */
	private function validate(): void {
		if ( !filter_var(trim("$this"), FILTER_VALIDATE_URL) )
			throw new Exception("Invalid HTTP Url: '$this'");
	}

	/**
	 * Internal: Parse the full URL and iitialize the properties.
	 */
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

	/**
	 * Check if the full URL use HTTPS protocol.
	 * 
	 * @return boolean
	 */
	public function safe(): bool {
		return $this->protocol === 'https';
	}

	/**
	 * Return the full URL path, including query and fragment.
	 * 
	 * @return string
	 */
	public function fullpath(): string {
		$fullpath = $this->path;
		if ( $this->query )    $fullpath .= "?$this->query";
		if ( $this->fragment ) $fullpath .= "#$this->fragment";
		return $fullpath;
	}

	/**
	 * Get a value of an URL query key.
	 * 
	 * @param  string     $key
	 * @param  mixed|null $default
	 * @return mixed
	 */
	public function query(string $key, mixed $default = null): mixed {
		parse_str($this->query, $query);
		return $query[$key] ?? $default;
	}
}