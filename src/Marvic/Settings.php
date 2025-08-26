<?php

namespace Marvic;

use InvalidArgumentException;

final class Settings {

	/** @var array<string, array> */
	private array $data = [];

	public function __construct(array $data = []) {
		$this->data = $data;
	}

	private function validateKey(string $key): string {
		if ( !preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]$/', $key) ) return $key;
		throw new InvalidArgumentException("Invalid setting key: $key");
	}

	public function has(string $key): bool {
		$this->validateKey($key);
		[$data, $parts] = [$this->data, explode('.', $key)];

		foreach ($parts as $part) {
			if ( !isset($data[$part]) ) return false;
			$data = $data[is_numeric($part) ? (int)$part : $part];
		}
		return true;
	}

	public function get(string $key, mixed $default = null): mixed {
		if ( !$this->has($key) ) return $default;

		[$data, $parts] = [$this->data, explode('.', $key)];

		foreach ($parts as $part)
			$data = $data[is_numeric($part) ? (int)$part : $part];
		return $data ?? $default;
	}

	public function set(string $key, mixed $value): void {
		$this->validateKey($key);
		$callback = function(&$data, $parts, $value) use (&$callback) {
			$part = array_shift($parts);
			if ($part === null) { $data = $value; return; }
			$part = is_numeric($part) ? (int)$part : $part;

			if ( !isset($data[$part]) ) $data[$part] = [];;

			$callback($data[$part], $parts, $value);
		};
		$callback($this->data, explode('.', $key), $value);
	}

	public function is(string $key, mixed $value): bool {
		return $this->has($key) && $this->get($key) === $value;
	}

	public function empty(string $key): bool {
		return $this->has($key) && empty($this->get($key));
	}

	public function all(): array {
		return $this->data;
	}

	public function merge(self $settings): void {
		$this->data = array_merge($this->data, $settings->all());
	}
}