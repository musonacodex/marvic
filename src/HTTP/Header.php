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
	private string $name;

	/** @var string */
	private string $value;

	public function __construct(string $name, string $value = '') {
		if ( !$this->isValidName($name) ) {
			$message = "Invalid HTTP header name: $name";
			throw new InvalidArgumentException($message);
		}
		$this->name  = $name;
		$this->value = $value;
	}

	public function __get(string $name): string {
		return $this->$name;
	}

	public function __toString(): string {
		return "$this->name: $this->value";
	}

	private function isValidName($name): bool {
		return (bool) preg_match('/^[a-zA-Z][a-zA-Z0-9-]*$/', $name);
	}

	public function toArray(): array {
		return [$this->name, $this->value];
	}

	public function send(): void {
		header("$this");
	}
}