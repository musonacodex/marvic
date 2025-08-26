<?php

namespace Marvic\HTTP\Header;

use Marvic\HTTP\Header;

/**
 * HTTP header collection and manager.
 * 
 * @package Marvic\HTTP\Header
 * @version 1.1.1
 */
final class Collection {
	/** @var array<string, object> */
	private array $collection = [];

	public function __construct(Header ...$collection) {
		foreach ($collection as $header)
			$this->collection[strtolower($header->name)] = $header;
	}

	public function __toString(): string {
		return implode("\r\n", array_map(fn($item) => "$item", 
			array_values($this->collection)));
	}

	public function has(string $name): bool {
		return array_key_exists(strtolower($name), $this->collection);
	}

	public function get(string $name, mixed $default = null): ?string {
		if (! $this->has($name) ) return $default;
		return $this->collection[strtolower($name)]->value;
	}

	public function set(string $name, string|int $value): void {
		$this->collection[strtolower($name)] = new Header($name, "$value");
	}

	public function is(string $name, string|int $value): bool {
		return $this->has($name) && strtolower($this->get($name)) === strtolower("$value");
	}

	public function remove(string $name): void {
		$this->collection[strtolower($name)] = new Header($name, null);
	}

	public function all(): array {
		return array_merge(...array_map(fn($item) => $item->toArray(),
			array_values($this->collection)));
	}

	public function send(): void {
		foreach (array_values($this->collection) as $item) $item->send();
	}

	public static function getFromGlobals(): self {
		$headers = 
			function_exists('getallheaders') ? getallheaders() : $_SERVER;

		foreach ($headers as $key => $value) {
			$newkey = ucwords(str_replace(['HTTP_','_'], ['','-'], $key), '-');
			$headers[] = new Header($newkey, $value); unset($headers[$key]);
		}
		return new self(...$headers);
	}
}