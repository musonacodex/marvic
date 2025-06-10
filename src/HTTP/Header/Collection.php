<?php

namespace Marvic\HTTP\Header;

use Marvic\HTTP\Header;

/**
 * HTTP header collection and manager.
 * 
 * @package Marvic\HTTP\Header
 * @version 1.1.0
 */
final class Collection {
	/** @var array<string, object> */
	private array $collection = [];

	public function __construct(Header ...$collection) {
		foreach ($collection as $item)
			$this->collection[strtolower($item->name)] = $item;
	}

	public function __toString(): string {
		return implode("\n", array_map(fn($item) => "$item", 
			array_values($this->collection)));
	}

	public function has(string $name): bool {
		return array_key_exists(strtolower($name), $this->collection);
	}

	public function get(string $name, ?string $default = null): ?string {
		return $this->has($name) ? $this->collection[$name]->value : $default;
	}

	public function set(string $name, string $value): void {
		if ( $this->has($name) ) $this->collection[$name]->value = $value;
	}

	public function is(string $name, string $value): bool {
		return $this->has($name) && strtolower($this->get($name)) === $value;
	}

	public function remove(string $name): void {
		unset($this->collection[$name]);
	}

	public function all(): array {
		return array_merge(...array_map(fn($item) => $item->toArray(),
			array_values($this->collection)));
	}

	public function send(): array {
		foreach (array_values($this->collection) as $item) $item->send();
	}

	public static function getFromGlobals(): self {
		$_headers = 
			function_exists('getallheaders') ? getallheaders() : $_SERVER;

		$headers = new self();
		foreach ($_headers as $key => $value) {
			$key = str_replace(['HTTP_','_'], ['','-'], $key);
			$key = ucwords($key, '-');
			$headers->set($key, $value);
		}
		return $headers;
	}
}