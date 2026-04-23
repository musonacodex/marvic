<?php

namespace Marvic\Http\Message;

use InvalidArgumentException;

final class Cookie {
	private const SAME_SITE_VALUES = ['Strict', 'Lax', 'None'];

	public readonly string $name;
	public readonly string $value;

	public readonly ?int	$maxAge;
	public readonly ?string $path;
	public readonly ?string $domain;
	public readonly ?bool   $secure;
	public readonly ?bool   $httpOnly;
	public readonly ?string $sameSite;

	public function __construct(
		string  $name,
		string  $value,
		?int    $maxAge  = null,
		?string $path     = null,
		?string $domain   = null,
		?bool   $secure   = null,
		?bool   $httpOnly = null,
		?string $sameSite = null
	) {
		$this->name     = $this->validateName($name);
		$this->value    = $value;
		$this->maxAge  = $maxAge;
		$this->path     = $path;
		$this->domain   = $domain;
		$this->secure   = $secure;
		$this->httpOnly = $httpOnly;
		$this->sameSite = $this->validateSameSite($sameSite);
	}

	public function __toString(): string {
		$output  = "$this->name=$this->value";
		$output .= is_null($this->maxAge)  ? '' : "; Max-Age=$this->maxAge";
		$output .= is_null($this->path)     ? '' : "; Path=$this->path";
		$output .= is_null($this->domain)   ? '' : "; Domain=$this->domain";
		$output .= $this->secure !== true   ? '' : "; Secure";
		$output .= $this->httpOnly !== true ? '' : "; HttpOnly";
		$output .= is_null($this->sameSite) ? '' : "; SameSite=$this->sameSite";
		return $output;
	}

	private function validateName(string $name): string {
		$name = trim($name);
		if ($name === '') {
			$message = 'Cookie name cannot be empty';
			throw new InvalidArgumentException($message);
		}
		if (preg_match('/[=,; \t\r\n\013\014]/', $name)) {
			$message = "Cookie name contains invalid characters: $name";
			throw new InvalidArgumentException($message);
		}
		return $name;
	}

	private function validateSameSite(?string $sameSite): ?string {
		if ($sameSite === null) return null;
		$sameSite = ucfirst(strtolower(trim($sameSite)));
		if (in_array($sameSite, self::SAME_SITE_VALUES, true)) return $sameSite;

		$message = "SameSite must be Strict, Lax, or None. Got: $sameSite";
		throw new InvalidArgumentException($message);
	}

	public function expired(): bool {
		if ($this->maxAge === null) return false;
		return time() > $this->maxAge;
	}
}