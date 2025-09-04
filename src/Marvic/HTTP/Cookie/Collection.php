<?php

namespace Marvic\HTTP\Cookie;

use Marvic\HTTP\Cookie;

/**
 * HTTP cookie collection.
 * 
 * @package Marvic\HTTP\Cookie
 */
final class Collection {
	/** 
	 * A list of HTTP cookie instances.
	 * 
	 * @var array<string, Marvic\HTTP\Cookie>
	 */
	private array $collection = [];

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param Marvic\HTTP\Cookie[] $collection
	 */
	public function __construct(Cookie ...$collection) {
		foreach ($collection as $cookie)
			$this->collection[strtolower($cookie->name)] = $cookie;
	}

	/**
	 * Return the raw HTTP Message Cookie Collection.
	 * 
	 * @return string
	 */
	public function toString(bool $request = true): string {
		return $request
			? "Cookie: " . implode("; ", array_map(fn($item) => "$item", 
				array_values($this->collection)))

			: implode("\r\n", array_map(fn($item) => "$item", 
				array_values($this->collection)))
		;
	}

	/**
	 * Check if the cookie exists from the given name.
	 * 
	 * @param  string  $name
	 * @return boolean
	 */
	public function has(string $name): bool {
		return array_key_exists(strtolower($name), $this->collection);
	}

	/**
	 * Get the cookie value from the given name.
	 * 
	 * @param  string      $name
	 * @param  mixed|null  $default
	 * @return string|null 
	 */
	public function get(string $name, ?string $default = null): ?Cookie {
		if (! $this->has($name) ) return $default;
		return $this->collection[strtolower($name)]->value;
	}

	/**
	 * Set a new cookie or replace an existent header.
	 * 
	 * @param string     $name
	 * @param string|int $value
	 * @param array      $options
	 */
	public function set(string $name, string $value, array $options = []): void {
		$this->collection[strtolower($name)] = new Cookie($name, $value, $options);
	}

	/**
	 * Remove an HTTP cookie.
	 * 
	 * @param string $name
	 */
	public function remove(string $name): void {
		if ( $this->has($name) ) $this->collection[strtolower($name)]->remove();
	}

	/**
	 * Return all HTTP cookies as an associative array.
	 * 	
	 * @return array
	 */
	public function all(): array {
		return array_map(fn($item) => $item->toArray(),
			array_values($this->collection));
	}

	/**
	 * Send all HTTP cookies to client.
	 */
	public function send(): void {
		foreach (array_values($this->collection) as $item) $item->send();
	}
}