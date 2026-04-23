<?php

namespace Marvic\Http\Message\Cookie;

use RuntimeException;
use InvalidArgumentException;
use Marvic\Http\Message\Cookie;

final class Collection {
	private array $cookies   = [];
	private bool  $immutable = false;
	
	public function __construct(array $cookies = []) {
		foreach ($cookies as $key => $value) $this->set($key, $value);
		$this->immutable = !empty($cookies);
	}
	
	public function __toString(): string {
		$callback = fn(Cookie $cookie) => $cookie->__toString();
		return implode('; ', array_map($callback, $this->cookies));
	}
	
	private function checkImmutability(): void {
		if (! $this->immutable ) return;
		throw new RuntimeException('Cookie collection is immutable');
	}
	
	public function toArray(): array {
		$callback = fn (Cookie $cookie) => $cookie->value;
		$names  = array_keys($this->cookies);
		$values = array_map($callback, array_values($this->cookies));
		return array_combine($names, $values);
	}
	
	public function toHeaders(): array {
		$callback = fn(Cookie $cookie) => "Set-Cookie: $cookie";
		return array_map($callback, $this->cookies);
	}
	
	public function all(): array {
		return $this->cookies;
	}

	public function count(): int {
		return count($this->cookies);
	}
	
	public function empty(): bool {
		return $this->count() === 0;
	}

	public function clear(): self {
		$this->checkImmutability();
		$this->cookies = [];
		return $this;
	}

	public function remove(string $name): self {
		$this->checkImmutability();
		unset($this->cookies[$name]);
		return $this;
	}
		
	public function has(string $name): bool {
		return isset($this->cookies[$name]);
	}

	public function get(string $name): ?Cookie {
		return $this->cookies[$name] ?? null;
	}
	
	public function getValue(string $name, ?string $default = null): ?string {
		$cookie = $this->get($name);
		return $cookie ? $cookie->value : $default;
	}

	public function set(string $name, string|int $value, $options = []): self {
		$this->checkImmutability();

		$cookie = new Cookie($name, "$value", 
			maxAge:   $options['maxAge']   ?? null, 
			path:     $options['path']     ?? null, 
			domain:   $options['domain']   ?? null, 
			secure:   $options['secure']   ?? null, 
			httpOnly: $options['httpOnly'] ?? null, 
			sameSite: $options['sameSite'] ?? null
		);
		$this->cookies[$cookie->name] = $cookie;
		
		return $this;
	}

	public static function fromGlobals(): self {
		return new self($_COOKIE);
	}
}
