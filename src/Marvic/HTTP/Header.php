<?php

namespace Marvic\HTTP;

/**
 * HTTP Header Representation.
 * 
 * @package Marvic\HTTP
 * @version 1.0.1
 */
final class Header {
	/** @var string */
	public readonly string $name;

	/** @var string|null */
	public readonly ?string $value;

	public function __construct(string $name, ?string $value = null) {
		if ( !$this->isValidName($name) ) {
			$message = "Invalid HTTP header name: $name";
			throw new InvalidArgumentException($message);
		}
		$this->name  = $name;
		$this->value = $value;
	}

	public function __toString(): string {
		return is_null($this->value) ? '' : "$this->name: $this->value";
	}

	private function isValidName($name): bool {
		return (bool) preg_match('/^[a-zA-Z][a-zA-Z0-9-]*$/', $name);
	}

	public function toArray(): array {
		return [$this->name, $this->value];
	}

	public function send(): void {
		if ( is_null($this->value) )
			header_remove($this->name); 
		else
			header("$this");
	}
}