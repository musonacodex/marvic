<?php

namespace Marvic\HTTP\Cookie;

use Marvic\HTTP\Cookie;

/**
 * HTTP cookie collection and manager.
 * 
 * @package Marvic\HTTP\Cookie
 * @version 1.1.0
 */
final class Collection {
	/** @var array<string, object> */
	private array $collection = [];

	public function __construct(Header ...$collection) {
		foreach ($collection as $item)
			$this->collection[$item->name] = $item;
	}

	public function toString(bool $readonly = true): string {
		return $readonly
			? "Cookie: " . implode("; ", array_map(
				fn($item) => $item->toString(true), 
				array_values($this->collection)))

			: implode("\n", array_map(
				fn($item) => $item->toString(false), 
				array_values($this->collection)))
		;
	}

	public function has(string $name): bool {
		return array_key_exists($name, $this->collection);
	}

	public function get(string $name, ?string $default = null): ?Cookie {
		return $this->has($name) ? $this->collection[$name] : $default;
	}

	public function set(string $name, string $value, array $options = []): void {
		if ( $this->has($name) )
			$this->get($name)->set($value, $options);
	}

	public function remove(string $name): void {
		unset($this->collection[$name]);
	}

	public function all(): array {
		return array_map(fn($item) => $item->toArray(),
			array_values($this->collection));
	}

	public function send(): array {
		foreach (array_values($this->collection) as $item) $item->send();
	}

	public static function getFromGlobals(): self {
		$cookies = new self();
		foreach ($_COOKIE as $key => $value) $cookies->set($key, $value);
		return $cookies;
	}
}