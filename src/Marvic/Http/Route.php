<?php

namespace Marvic\Http;

use Exception;
use RuntimeException;
use ReflectionFunction;
use InvalidArgumentException;
use Marvic\Http\Message\Request;
use Marvic\Http\Message\Request\Methods;
use Marvic\Http\Message\Response;

final class Route {
	private array $stacks = [];
	public readonly string $path;
	public readonly bool   $middle;
	
	public function __construct(string $path, bool $middle) {
		$this->path   = $path;
		$this->middle = $middle;
	}

	public function __call(string $name, array $arguments): self {
		if ( empty($arguments) ) {
			$message = "Arguments are required: ". __CLASS__ ."::$name()";
			throw new InvalidArgumentException($message);
		}
		if ($name !== strtolower($name) || !Methods::has(strtoupper($name))) {
			$message = "Undefined method: ". __CLASS__ ."::$name()";
			throw new RuntimeException($message);
		}
		$this->match([strtoupper($name)], ...$arguments);
		return $this;
	}

	private function handleRequest(Callable $handler, Request $request,
		Response $response, Callable $next): void
	{
		$reflection = new ReflectionFunction($handler);
		$parameters = $reflection->getParameters();
		if ( count($parameters) > 3 ) { $next(); return; }

		try {
			call_user_func_array($handler, [$request, $response, $next]);
		} catch (Exception $error) {
			$next($error);
		}
	}

	private function handleError(Callable $handler, mixed $error,
		Request $request, Response $response, Callable $next): void
	{
		$reflection = new ReflectionFunction($handler);
		$parameters = $reflection->getParameters();
		if ( count($parameters) !== 4 ) { $next($error); return; }

		try {
			call_user_func_array($handler, [$error, $request, $response, $next]);
		} catch (Exception $newerror) {
			$next($newerror);
		}
	}

	private function validateHandler($handler) {
		if ( is_array($handler) ) {
			if (count($handler) === 2 && class_exists($handler[0])) {
				$handler[0] = new $handler[0]();
				$handler = fn(...$args) => call_user_func_array($handler, $args);
			} else {
				foreach ($handler as $item) $this->validateHandler($item);
			}
		}
		if (! is_callable($handler) ) {
			$message = "Invalid route handler: $this->path";
			throw new InvalidArgumentException($message);
		}
		$reflection = new ReflectionFunction($handler);
		$parameters = $reflection->getParameters();
		
		if ( empty($parameters) ) {
			$message = "Handler arguments is required";
			throw new InvalidArgumentException($message);
		}
		return $handler;
	}

	public function handlesMethod(string $method): bool {
		return in_array($method, array_keys($this->stacks));
	}

	public function any(...$handlers): self {
		return $this->match(Methods::all(), ...$handlers);
	}

	public function match(array $methods, ...$handlers): self {
		foreach ($methods as $method) {
			if (! array_key_exists($method, $this->stacks) )
				$this->stacks[$method] = [];
			$handlers = array_map([$this, 'validateHandler'], $handlers);
			array_push($this->stacks[$method], ...$handlers);
		}
		return $this;
	}

	public function view(string $path, array $data = []): self {
		$callback = fn($request, $response) => $response->render($path, $data);
		return $this->match([Methods::GET], $callback);
	}

	public function redirect(string $path, int $status = 302): self {
		$callback = fn($request, $response) => $response->redirect($path, $data);
		return $this->match([Methods::GET], $callback);
	}

	public function dispatch(Request $req, Response $res, Callable $done,
		mixed $error = null): void
	{
		$stack = $this->stacks[$req->method];
		$next = function($error = null) use (&$next, &$stack, $req, $res, $done) {
			$stop = in_array($error, ['route', 'router']);
			if ($stop || empty($stack) || $res->ended) return $done($error);

			$handler = array_shift($stack);

			if ($error)
				$this->handleError($handler, $error, $req, $res, $next);
			else
				$this->handleRequest($handler, $req, $res, $next);
		};
		$next($error);
	}
}
