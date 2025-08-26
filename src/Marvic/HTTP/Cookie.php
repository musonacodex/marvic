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
	public readonly string $name;

	/** @var string */
	public readonly string $value;

	/** @var string|null */
	public readonly ?string $domain;

	/** @var string|null */
	public readonly ?string $path;

	/** @var integer|null */
	public readonly ?int $expiresAt;

	/** @var string */
	public readonly string $sameSite;

	/** @var bool */
	public readonly bool $secure;

	/** @var bool */
	public readonly bool $httpOnly;

	/** @var bool */
	private bool $forget = false;

	public function __construct(string $name, string $value = '', array $options = []) {
		if ( !$this->isValidName($name) ) {
			$message = "Invalid HTTP cookie name: $name";
			throw new InvalidArgumentException($message);
		}
		$this->name      = $name;
		$this->value     = $value;
		$this->domain    = $options['domain']     ?? null;
		$this->path      = $options['path']       ?? null;
		$this->expiresAt = ($options['expiresAt'] ?? 3600) + time();
		$this->sameSite  = $options['sameSite']   ?? 'Lax';
		$this->secure    = $options['secure']     ?? false;
		$this->httpOnly  = $options['httpOnly']   ?? false;
	}

	public function __toString(): string {
		$output = [];
		if ( $this->path   ) array_push($output, "Path=$this->path");
		if ( $this->domain ) array_push($output, "Domain=$this->domain");

		if ( $this->remove )
			array_push($output, "Max-Age=". (string) time() - 3600);
		
		else if ( $this->expiresAt )
			array_push($output, "Max-Age=$this->expiresAt");

		if ( $this->secure   ) array_push($output, 'Secure');
		if ( $this->httpOnly ) array_push($output, 'HttpOnly');
		if ( $this->sameSite ) array_push($output, "SameSite=$this->sameSite");

		if ( empty($output) ) return "$this->name=$$this->value";
		return "$this->name=$this->value; " . implode('; ', $output);
	}

	public function toString(bool $request = true): string {
		return $request ? "$this->name=$this->value" : "Set-Cookie: $this";
	}

	public function toArray(): array {
		return ['name'=>$this->name, 'value'=>$this->value,
			'expiresAt'=>$this->expiresAt, 'path'=>$this->path,
			'domain'=>$this->domain, 'secure'=>$this->secure,
			'httpOnly'=>$this->httpOnly, 'sameSite'=>$this->sameSite,
		];
	}

	private function isValidName($name): bool {
		return (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]*$/', $name);
	}

	public function remove(): void {
		$this->forget = true;
	}

	public function send(): void {
		setcookie($this->name, $this->value, [
			'expires'  => $this->forget ? time() - 3600 : $this->expiresAt,
			'path'     => $this->path,
			'domain'   => $this->domain,
			'secure'   => $this->secure,
			'httponly' => $this->httpOnly,
			'samesite' => $this->sameSite,
		]);
	}
}