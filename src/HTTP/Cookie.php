<?php

namespace Marvic\HTTP;

/**
 * HTTP Cookie Representation.
 * 
 * @package Marvic\HTTP
 * @version 1.0.0
 */
final class Cookie {
	/** @var string */
	private string $name;

	/** @var string */
	private string $value;

	/** @var string|null */
	private ?string $domain;

	/** @var string|null */
	private ?string $path;

	/** @var integer|null */
	private ?int $expiresAt;

	/** @var string */
	private string $sameSite;

	/** @var bool */
	private bool $secure;

	/** @var bool */
	private bool $httpOnly;

	public function __construct(string $name, string $value = '', array $options = []) {
		if ( !$this->isValidName($name) ) {
			$message = "Invalid HTTP cookie name: $name";
			throw new InvalidArgumentException($message);
		}
		$this->name = $name;
		$this->set($value, $options);
	}

	public function __get(string $name) {
		return $this->$name;
	}

	public function __toString(): string {
		$output = [];
		if ( $this->path )     array_push($output, "Path=$this->path");
		if ( $this->domain)    array_push($output, "Domain=$this->domain");
		if ( $this->expiresAt) array_push($output, "Max-Age=$this->expiresAt");
		if ( $this->secure )   array_push($output, 'Secure');
		if ( $this->httpOnly ) array_push($output, 'HttpOnly');
		if ( $this->sameSite ) array_push($output, "SameSite=$this->sameSite");

		return "$this->name=$this->value; " . implode('; ', $output);
	}

	private function isValidName($name): bool {
		return (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]*$/', $name);
	}

	public function set(string $value, array $options = []): void {
		$this->value     = rawurldecode($value);
		$this->domain    = $options['domain']    ?? null;
		$this->path      = $options['path']      ?? null;
		$this->expiresAt = $options['expiresAt'] ?? null;
		$this->secure    = $options['secure']    ?? false;
		$this->httpOnly  = $options['httpOnly']  ?? false;
		$this->sameSite  = $options['sameSite']  ?? 'Lax';
	}

	public function toString(bool $readonly = true): string {
		return $readonly ? "$this->name=$this->value" : "Set-Cookie: $this";
	}

	public function toArray(): array {
		return ['name'=>$this->name, 'value'=>$this->value,
			'expiresAt'=>$this->expiresAt, 'path'=>$this->path,
			'domain'=>$this->domain, 'secure'=>$this->secure,
			'httpOnly'=>$this->httpOnly, 'sameSite'=>$this->sameSite,
		];
	}

	public function send(): void {
		header( $this->toString(false) );
	}
}