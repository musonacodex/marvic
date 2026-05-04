<?php

namespace Marvic\View;

use InvalidArgumentException;

final class EngineManager {
	private array $collection = [];

	public function __construct() {
		$this->register('php', $this->getPhpTemplateRenderer());
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

	public function getPhpTemplateRenderer(): Callable {
		return function(string $path, array $data = []): string {
			$directory = pathinfo($path, PATHINFO_DIRNAME);
			$oldPaths = get_include_path();
			set_include_path($directory);
			extract($data);
			ob_start();
			require $path;
			$output = ob_get_clean();
			set_include_path($oldPaths);
			return $output;
		};
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
		$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		$renderes  = $this->collection[ltrim($extension, '.')];

		foreach ($renderes as $render) {
			$compiled = $render($path, $data);
			if ( is_string($compiled) ) return $compiled;
		}
	}
}
