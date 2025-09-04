<?php

namespace Marvic\HTTP;

/**
 * ImmutableHTTP Cookie.
 * 
 * @package Marvic\HTTP
 */
final class Cookie {
	/**
	 * HTTP Cookie Name.
	 * 
	 * @var string
	 */
	public readonly string $name;

	/** 
	 * HTTP Cookie Value.
	 * 
	 * @var string
	 */
	public readonly string $value;

	/**
	 * HTTP Cookie Domain.
	 * 
	 * @var string|null
	 */
	public readonly ?string $domain;

	/**
	 * HTTP Cookie Path.
	 * 
	 * @var string|null
	 */
	public readonly ?string $path;

	/**
	 * HTTP Cookie Expiration Time.
	 * 
	 * @var integer|null
	 */
	public readonly ?int $expiresAt;

	/**
	 * HTTP Cookie SameSite.
	 * 
	 * @var string
	 */
	public readonly string $sameSite;

	/**
	 * HTTP Cookie Secure.
	 * 
	 * @var boolean
	 */
	public readonly bool $secure;

	/**
	 * HTTP Cookie HTTP Only.
	 * 
	 * @var boolean
	 */
	public readonly bool $httpOnly;

	/**
	 * Do the cookie need be forgotten?
	 * 
	 * @var boolean
	 */
	private bool $forget = false;

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param string $name
	 * @param string $value
	 * @param array  $options
	 */
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

	/**
	 * Return the array representation.
	 * 
	 * @return array
	 */
	public function toArray(): array {
		return ['name'=>$this->name, 'value'=>$this->value,
			'expiresAt'=>$this->expiresAt, 'path'=>$this->path,
			'domain'=>$this->domain, 'secure'=>$this->secure,
			'httpOnly'=>$this->httpOnly, 'sameSite'=>$this->sameSite,
		];
	}

	/**
	 * Validate the cookie name.
	 * 
	 * @param  string  $name
	 * @return boolean
	 */
	private function isValidName(string $name): bool {
		return (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]*$/', $name);
	}

	/**
	 * Remove the cookie.
	 */
	public function remove(): void {
		$this->forget = true;
	}

	/**
	 * Send the cookie to browser.
	 */
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