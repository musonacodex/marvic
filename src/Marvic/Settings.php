<?php

namespace Marvic;

use InvalidArgumentException;

/**
 * Marvic Application Settings Class.
 *
 * This class is responsable to save and handle configurations of a
 * Marvic Web Application. All configurations are stored and centralized
 * inside of '$data' property as array, that it works as a 'key-value'
 * repository.
 *
 * Examples of Use:
 *
 * $settings = new Settings([
 *     'debug' => true,
 *     'timezone' => 'UTC',
 *     'cache' => [
 *         'enabled' => false,
 *         'driver' => 'file',	
 *     ],
 * ]);
 *
 * // Check and get balues.
 * if ($settings->has('debug') && $sattings->is('debug', true)) {
 *     echo 'Enabled debug mode';
 * }
 *
 * // Change a setting
 * $settings->set('timezone', 'Africa\Luanda');
 *
 * // Get a default value.
 * $driver = $settings->get('cache.driver', 'file');
 *
 * // Merge many settings.
 * $settings->merge([
 *     'cache' => [
 *         'enabled' => true,
 *         'driver' => 'redis',	
 *     ],
 * ]);
 *
 * @package Marvic
 */
final class Settings {
	/**
	 * Key-Value repository, used to save all configurations.
	 * 
	 * @var array<string, mixed>
	 */
	private array $data = [];

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param array $data
	 */
	public function __construct(array $data = []) {
		$this->data = $data;
	}

	/**
	 * Validate alphanumeric keys (and underlines) separated by dots.
	 * 
	 * @param  string $key
	 * @return string
	 */
	private function validateKey(string $key): string {
		if ( !preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]$/', $key) ) return $key;
		throw new InvalidArgumentException("Invalid setting key: $key");
	}

	/**
	 * Check if a setting data exists.
	 * 
	 * @param  string  $key
	 * @return boolean
	 */
	public function has(string $key): bool {
		$this->validateKey($key);
		[$data, $parts] = [$this->data, explode('.', $key)];

		foreach ($parts as $part) {
			if ( !isset($data[$part]) ) return false;
			$data = $data[is_numeric($part) ? (int)$part : $part];
		}
		return true;
	}

	/**
	 * Get a value of a setting data, with dot-notation support.
	 * 
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get(string $key, mixed $default = null): mixed {
		if ( !$this->has($key) ) return $default;

		[$data, $parts] = [$this->data, explode('.', $key)];

		foreach ($parts as $part)
			$data = $data[is_numeric($part) ? (int)$part : $part];
		return $data ?? $default;
	}

	/**
	 * Set a value of a setting data, with dot-notation support.
	 * 
	 * @param string $key
	 * @param mixed  $value
	 */
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

	/**
	 * Check if the entry value is the same value of a setting data.
	 * 
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return boolean
	 */
	public function is(string $key, mixed $value): bool {
		return $this->has($key) && $this->get($key) === $value;
	}

	/**
	 * Check if a setting data is empty.
	 * 
	 * @param  string  $key
	 * @return boolean
	 */
	public function empty(string $key): bool {
		return $this->has($key) && empty($this->get($key));
	}

	/**
	 * Show all configurations in array.
	 * 
	 * @return array
	 */
	public function all(): array {
		return $this->data;
	}

	/**
	 * Merge all configurations in one time.
	 * 
	 * @param self $settings
	 */
	public function merge(self $settings): void {
		$this->data = array_merge($this->data, $settings->all());
	}
}