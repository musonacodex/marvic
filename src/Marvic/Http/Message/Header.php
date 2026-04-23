<?php

namespace Marvic\Http\Message;

use InvalidArgumentException;

final class Header {
	public readonly string $name;
	public readonly array  $values;

	public function __construct(string $name, string|array $values) {
		$this->name   = $this->normalizeName($name);
		$this->values = $this->normalizeValues($values);
	}

	public function __toString(): string {
		return "$this->name: " . implode(', ', $this->values);
	}

	private function normalizeName(string $name): string {
		$name = strtolower(trim($name));
		if ($name !== '')
            return implode('-', array_map('ucfirst', explode('-', $name)));
		throw new InvalidArgumentException('Header name cannot be empty');
	}

	private function normalizeValues(string|array $values): array {
		$normalized = is_array($values) ? $values : [$values];
		return array_map('trim', $normalized);
	}
}
