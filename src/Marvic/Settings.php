<?php

namespace Marvic;

final class Settings {
	private array $data = [];
	private array $keyCache = [];
	
	private function getKeys(string $key): array {
		if (! array_key_exists($key, $this->keyCache) )
			$this->keyCache[$key] = explode('.', $key);
		return $this->keyCache[$key];
	}

	private function getRecursive(array $array, array $keys): mixed {
		$key = array_shift($keys);

		if (! array_key_exists($key, $array) ) return null;
		
		if ( empty($keys) ) return $array[$key];

		if (! is_array($array[$key]) ) return null;

		return $this->getRecursive($array[$key], $keys);
	}

	private function setRecursive(array &$array, array $keys, mixed $value): void {
		$key = array_shift($keys);
	
		if ( empty($keys) ) { $array[$key] = $value; return; }

		if (!array_key_exists($key, $array) || !is_array($array[$key]) )
			$array[$key] = [];

		$this->setRecursive($array[$key], $keys, $value);
	}

	private function removeRecursive(array &$array, array $keys): void {
		$key = array_shift($keys);
	
		if ( empty($keys) ) { unset($array[$key]); return; }

		if (array_key_exists($key, $array) || is_array($array[$key]) ) {
			$this->getRecursive($array[$key], $keys, $value);
			if ( empty($array[$keys]) ) unset($array[$keys]);
		}
	}

	public function all(): array {
		return $this->data;
	}

	public function clearKeyCache(): void {
		$this->keyCache = [];
	}

	public function has(string $key): bool {
		$keys = $this->getKeys($key);
		return $this->getRecursive($this->data, $keys) !== null;
	}

	public function remove(string $key): void {
		$keys = $this->getKeys($key);
		$this->removeRecursive($this->data, $keys);
	}

	public function merge(array $settings = []): void {
		$this->data = array_merge($this->data, $settings);
	}

	public function set(string $key, mixed $value): void {
		$keys = $this->getKeys($key);
		$this->setRecursive($this->data, $keys, $value);
	}

	public function get(string $key, mixed $default = null): mixed {
		$keys = $this->getKeys($key);
		return $this->getRecursive($this->data, $keys) ?? $default;
	}

	public function enable(string $key): void {
		$this->set($key, true);
	}

	public function disable(string $key): void {
		$this->set($key, false);
	}

	public function enabled(string $key): bool {
		$this->get($key) === true;
	}

	public function disabled(string $key): bool {
		$this->get($key) === false;
	}
}
