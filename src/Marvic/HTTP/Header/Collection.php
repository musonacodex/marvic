<?php

namespace Marvic\HTTP\Header;

use Marvic\HTTP\Header;

/**
 * HTTP header collection.
 * 
 * @package Marvic\HTTP\Header
 */
final class Collection {
	/** 
	 * A list of HTTP header instances.
	 * 
	 * @var array<string, Marvic\HTTP\Header>
	 */
	private array $collection = [];

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param Marvic\HTTP\Header[] $collection
	 */
	public function __construct(Header ...$collection) {
		foreach ($collection as $header)
			$this->collection[strtolower($header->name)] = $header;
	}

	/**
	 * Return the raw HTTP Message Header Collection.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		return implode("\r\n", array_map(fn($header) => "$header", 
			array_values($this->collection)));
	}

	/**
	 * Check if the header exists from the given name.
	 * 
	 * @param  string  $name
	 * @return boolean
	 */
	public function has(string $name): bool {
		return array_key_exists(strtolower($name), $this->collection);
	}

	/**
	 * Get the header value from the given name.
	 * 
	 * @param  string      $name
	 * @param  mixed|null  $default
	 * @return string|null 
	 */
	public function get(string $name, mixed $default = null): ?string {
		if (! $this->has($name) ) return $default;
		return $this->collection[strtolower($name)]->value;
	}

	/**
	 * Set a new header or replace an existent header.
	 * 
	 * @param string     $name
	 * @param string|int $value
	 */
	public function set(string $name, string|int $value): void {
		$this->collection[strtolower($name)] = new Header($name, "$value");
	}

	/**
	 * Check if the given value is equals to value of a header.
	 * 
	 * @param  string     $name
	 * @param  string|int $value
	 * @return boolean
	 */
	public function is(string $name, string|int $value): bool {
		return $this->has($name) && strtolower($this->get($name)) === strtolower("$value");
	}

	/**
	 * Remove an HTTP header.
	 * 
	 * @param  string $name
	 */
	public function remove(string $name): void {
		$this->collection[strtolower($name)] = new Header($name, null);
	}

	/**
	 * Return laa HTTP header as an associative array.
	 * 	
	 * @return array
	 */
	public function all(): array {
		return array_merge(...array_map(fn($item) => $item->toArray(),
			array_values($this->collection)));
	}

	/**
	 * Send all HTTP headers to client.
	 */
	public function send(): void {
		foreach (array_values($this->collection) as $item) $item->send();
	}
}