<?php

namespace Marvic\HTTP;

/**
 * Imutable HTTP Message Header.
 *
 * This class represents an immutable header for HTTP message, composed
 * by a key-value pair. It use PHP built-in functions to send headers
 * to the browser, but if your value is null, the header will be
 * removed.
 * 
 * @package Marvic\HTTP
 */
final class Header {
	/** 
	 * HTTP header name.
	 * 
	 * @var string
	 */
	public readonly string $name;

	/** 
	 * HTTP Header Value.
	 * 
	 * @var string|null
	 */
	public readonly ?string $value;

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param string      $name
	 * @param string|null $value
	 */
	public function __construct(string $name, ?string $value = null) {
		if ( !$this->isValidName($name) ) {
			$message = "Invalid HTTP header name: $name";
			throw new InvalidArgumentException($message);
		}
		$this->name  = $name;
		$this->value = $value;
	}

	/**
	 * Return the raw HTTP header representation.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		return is_null($this->value) ? '' : "$this->name: $this->value";
	}

	/**
	 * Validate the header name.
	 * 
	 * @param  string  $name
	 * @return boolean
	 */
	private function isValidName(string $name): bool {
		return (bool) preg_match('/^[a-zA-Z][a-zA-Z0-9-]*$/', $name);
	}

	/**
	 * Return the array representation.
	 * 
	 * @return array
	 */
	public function toArray(): array {
		return [$this->name, $this->value];
	}

	/**
	 * Send the header to browser or remove it if the value is null.
	 */
	public function send(): void {
		if (! is_null($this->value) ) header("$this");
		else header_remove($this->name);
	}
}
