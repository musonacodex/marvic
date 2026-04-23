<?php

namespace Marvic\Http\Message\Header;

use Marvic\Http\Message\Header;

final class Collection {
	private array $headers = [];

	public function __construct(array $headers = []) {
		foreach ($headers as $name => $values) $this->set($name, $values);
	}

	public function __toString(): string {
		$callback = fn(Header $header) => $header->__toString();
		return implode("\r\n", array_map($callback, $this->headers));
	}

	public function toArray(): array {
		$callback = fn (Header $header) => implode(', ', $header->values);
		$names  = array_keys($this->headers);
		$values = array_map($callback, array_values($this->headers));
		return array_combine($names, $values);
	}

	public function all(): array {
		return $this->headers;
	}

	public function count(): int {
		return count($this->headers);
	}

	public function empty(): bool {
		return $this->count() === 0;
	}

	public function clear(): self {
		$this->headers = [];
		return $this;
	}

	public function names(): array {
		$callback = fn(Header $header) => $header->name;
		return array_map($callback, $this->headers);
	}

	public function remove(string $name): self {
		unset($this->headers[strtolower($name)]);
		return $this;
	}

	public function has(string $name): bool {
		return isset($this->headers[strtolower($name)]);
	}

	public function get(string $name): ?Header {
		return $this->headers[strtolower($name)] ?? null;
	}

	public function getValues(string $name): array {
		$header = $this->get($name);
		return $header ? $header->values : [];
	}

	public function getValue(string $name, ?string $default = null): ?string {
		$values = $this->getValues($name);
		return $values ? $values[0] : $default;
	}

	public function set(string $name, string|array $values): self {
		$header = new Header($name, $values);
		$this->headers[strtolower($name)] = $header;
		return $this;
	}

	public function add(string $name, string|array $values): self {
		if (! $this->has($name) ) return $this->set($name, $values);

		$existing = $this->get($name);
		$currentValues = $existing->values;
		$newValues = is_array($values) ? $values : [$values];
		$merged = array_merge($currentValues, $newValues);

		return $this->set($name, $merged);
	}

	public function merge(self $headers): self {
		foreach ($headers->all() as $header)
			$this->add($header->name, $header->values);
		return $this;
	}

	public static function fromGlobals(): self {
		if (! function_exists('getallheaders') ) {
			function getallheaders(): array {
				$headers = [];
				foreach ($_SERVER as $key => $value) {
					if (! str_starts_with($key, 'HTTP_') ) continue;
					$name = str_replace('_', '-', substr($key, 5));
					$name = explode('-', strtolower($name));;
					$name = implode('-', array_map('ucfirst', $name));
					$headers[$name] = $value;
				}
				$specialHeaders = ['CONTENT_TYPE', 'CONTENT_LENGTH'];
				foreach ($specialHeaders as $key) {
					if (! isset($_SERVER[$key]) ) continue;
					$name = str_replace('_', '-', $key);
					$name = explode('-', strtolower($name));;
					$name = implode('-', array_map('ucfirst', $name));
					$headers[$name] = $value;
				}
				return $headers;
			}
		}
		return new self(getallheaders());
	}
}
