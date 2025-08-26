<?php

namespace Marvic\Routing\Route;

use ReflectionFunction;
use RuntimeException;
use BadMethodCallException;
use InvalidArgumentException;

use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Request\Methods;
use Marvic\HTTP\Message\Response;
use Marvic\Routing\Route;

final class Collection {
	private string $path;

	/** @var Marvic\Routing\Route[] */
	private array $collection = [];

	/** @var array [description] */
	private array $options = [];

	public function __construct(string $path, array $options = []) {
		$this->path    = $path;
		$this->options = $options;
	}

	public function __call(string $name, array $arguments) {
		if ( empty($arguments) ) {
			$message = "Arguments are required";
			throw new InvalidArgumentException($message);
		}
		if ( in_array($name, ['match', 'any', 'view', 'redirect']) ) {
			return $this->{$name}(...$arguments);
		}
		if ( in_array($name, array_map('strtolower', Methods::all())) ) {
			return $this->match([strtoupper($name)], ...$arguments);
		}
		$message = "Invalid route method: $name";
		throw new InvalidArgumentException($message);
	}

	private function validateHandler(Callable $handler): void { 
		if (! (new ReflectionFunction($handler))->getParameters() ) {
			$message = "Handler arguments is required";
			throw new InvalidArgumentException($message);
		}
	}

	public function handlesMethod(string $method): bool {
		return in_array($method, array_column($this->collection, 'method'));
	}

	public function match(array $methods, ...$handlers): self {
		$path = $this->path;
		array_map([$this, 'validateHandler'], $handlers);
		
		foreach ($handlers as $handler) {
			foreach ($methods as $method) {			
				$route = new Route($method, $path, $handler, [
					'end' => true,
				]);
				$this->collection[] = $route;
			}
		}
		return $this;
	}

	public function any(...$handlers): self {
		return $this->match(Methods::all(), ...$handlers);
	}

	public function view(string $name, array $data = []): self {
		return $this->get(function($request, $response) use ($name, $data) {
			$response->render($name, $data);
		});
	}

	public function redirect(string $uri, int $status = 302): self {
		return $this->get(function($request, $response) use ($uri, $status) {
			$response->redirect($uri, $status);
		});
	}

	public function use(...$middlewares): self {	
		$path = $this->path;

		foreach ($middlewares as $middleware) {
			foreach (Methods::all() as $method) {
				$this->validateHandler($middleware);
						
				$route = new Route($method, $path, $middleware, [
					'end' => false,
				]);
				$this->collection[] = $route;
			}
		}
		return $this;
	}

	public function findRoutes(string $method, string $path = '/', string $prefix = ''): array {
		$callback = fn($route) =>
			$route->method === $method && $route->match($path, $prefix);
		return array_filter($this->collection, $callback);
	}
}