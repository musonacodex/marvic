<?php

namespace Marvic\HTTP\Cookie;

use Marvic\HTTP\Cookie;

/**
 * HTTP cookie collection and manager.
 * 
 * @package Marvic\HTTP\Cookie
 * @version 1.1.1
 */
final class Collection {
	/** @var array<string, object> */
	private array $collection = [];

	public function __construct(Cookie ...$collection) {
		foreach ($collection as $cookie)
			$this->collection[strtolower($cookie->name)] = $cookie;
	}

	public function toString(bool $request = true): string {
		return $request
			? "Cookie: " . implode("; ", array_map(
				fn($item) => $item->toString(true), 
				array_values($this->collection)))

			: implode("\r\n", array_map(
				fn($item) => $item->toString(false), 
				array_values($this->collection)))
		;
	}

	public function has(string $name): bool {
		return array_key_exists(strtolower($name), $this->collection);
	}

	public function get(string $name, ?string $default = null): ?Cookie {
		if (! $this->has($name) ) return $default;
		return $this->collection[strtolower($name)]->value;
	}

	public function set(string $name, string $value, array $options = []): void {
		$this->collection[strtolower($name)] = new Cookie($name, $value, $options);
	}

	public function remove(string $name): void {
		if ( $this->has($name) ) $this->collection[strtolower($name)]->remove();
	}

	public function all(): array {
		return array_map(fn($item) => $item->toArray(),
			array_values($this->collection));
	}

	public function send(): void {
		foreach (array_values($this->collection) as $item) $item->send();
	}
}