<?php

namespace Marvic\View;

use InvalidArgumentException;

final class EngineManager {
	private mixed $fallback;

	private array $collection = [];

	public function __construct() {
		$this->fallback = $this->normalizeEngine([new PhpTemplateRenderer(), 'render']);
	}

	private function normalizeEngine(mixed $engine): Callable {
		if (is_array($engine) && count($engine) === 2 && is_string($engine[0])) {
			if (! class_exists($engine[0]) ) {
				$message = sprintf("Class not found: %s", $engine[0]);
				throw new InvalidArgumentException($message);
			}
			if (! method_exists($engine[0], $engine[1]) ) {
				$message = sprintf("method not found: %s::%s", ...$engine);
				throw new InvalidArgumentException($message);
			}
			$engine = new $engine[0];
			$engine = fn(string $path, array $data = []): string => 
				call_user_func_array($engine, [$path, $data]);
		}

		if (is_object($engine) && method_exists($engine, 'render')) {
			$engine = fn(string $path, array $data = []): string => 
				call_user_func_array([$engine, 'render'], [$path, $data]);
		}

		if ( is_callable($engine) ) return $engine;
	}

	public function register(string|array $extensions, mixed $engine): void {
		if (is_string($extensions)) $extensions = [$extensions];

		foreach ($extensions as $extension) {
			$extension = strtolower(ltrim($extension, '.'));

			if (!array_key_exists($extension, $this->collection))
				$this->collection[$extension] = [];

			$engine = $this->normalizeEngine($engine);
			$this->collection[$extension][] = $engine;
		}
	}

	public function unregister(string $extension): void {
		$extension = strtolower(ltrim($extension, '.'));
		unset($this->collection[$extension]);
	}

	public function render(string $path, array $data = []): string {
		$extension = ltrim(strtolower(pathinfo($path, PATHINFO_EXTENSION)), '.');
		
		if (! array_key_exists($extension, $this->collection) )
			return ($this->fallback)($path, $data);

		$renderes  = $this->collection[$extension];
		foreach ($renderes as $render) {
			$compiled = $render($path, $data);
			if ( is_string($compiled) ) return $compiled;
		}

		return ($this->fallback)($path, $data);
	}
}
