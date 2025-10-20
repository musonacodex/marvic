<?php

namespace Marvic\Routing;

use Exception;
use ReflectionFunction;
use InvalidArgumentException;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Request\Methods;
use Marvic\HTTP\Message\Response;

/**
 * Marvic Application Route.
 * 
 * This class represents an route, used to manage routes of an
 * application. Each route is associated by a path and handlers that
 * they handle in different HTTP methods.
 *
 * @package Marvic\Routing
 */
final class Route {
	/**
	 * The route path with support of parameters.
	 * 
	 * @var string
	 */
	public readonly string $path;

	/**
	 * The route matcher.
	 *
	 * @var Marvic\Routing\RouteMatcher
	 */
	public readonly RouteMatcher $matcher;

	/**
	 * The relationship between a method and your handler stack.
	 * 
	 * @var array<string, list>
	 */
	private array $stacks = [];

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param string $path
	 */
	public function __construct(string $path, RouteMatcher $matcher) {
		$this->path    = $path;
		$this->matcher = $matcher;
	}

	public function __call(string $name, array $arguments): self {
		if ($name !== strtolower($name) || !Methods::has(strtoupper($name))) {
			$message = "Invalid route method: ".strtoupper($name)." $this->path";
			throw new Exception($message);
		}
		$name = strtoupper($name);
		if (! array_key_exists($name, $this->stacks) ) $this->stacks[$name] = [];
		$handlers = array_map([$this, 'validateHandler'], $arguments);
		array_push($this->stacks[$name], ...$handlers);
		return $this;
	}

	private function handleRequest(Callable $handler, Request $request, Response $response,
		Callable $next): void
	{
		$reflection = new ReflectionFunction($handler);
		$parameters = $reflection->getParameters();
		if ( count($parameters) > 3 ) { $next(); return; }

		try {
			$body = call_user_func_array($handler, [$request, $response, $next]);
			if ( !$response->ended && !empty($body) ) $response->send($body);
		} catch (Exception $error) {
			$next($error);
		}
	}

	private function handleError(Callable $handler, mixed $error, Request $request,
		Response $response, Callable $next): void
	{
		$reflection = new ReflectionFunction($handler);
		$parameters = $reflection->getParameters();
		if ( count($parameters) !== 4 ) { $next($error); return; }

		try {
			$body = call_user_func_array($handler, [$error, $request, $response, $next]);
			if ( !$response->ended && !empty($body) ) $response->send($body);
		} catch (Exception $error) {
			$next($error);
		}
	}

	private function validateHandler($handler) {
		if ( is_array($handler) ) {
			if ( class_exists($handler[0]) ) $handler[0] = new $handler[0];

			$handler = function($req, $res, $next) use ($handler) {
				call_user_func_array($handler, [$req, $res, $next]);
				$next();
			};
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

	public function match(array $methods, ...$handlers): self {
		foreach (array_map('strtolower', $methods) as $method)
			$this->{$method}(...$handlers);
		return $this;
	}

	public function any(...$handlers): self {
		return $this->match(Methods::all(), ...$handlers);
	}

	public function view(string $name, array $data = []): self {
		return $this->get(function($req, $res) use ($name, $data) {
			$res->render($name, $data);
		});
	}
	
	public function redirect(string $uri, int $status = 302): self {
		return $this->get(function($req, $res) use ($uri, $status) {
			$res->redirect($uri, $status);
		});
	}

	public function dispatch(Request $req, Response $res, Callable $done,
		mixed $error = null): void
	{
		if ( $res->ended ) { $done($error); return; }

		$stack = $this->stacks[$req->method];
		if ( empty($stack) ) { $done(); return; }

		$next = function($error = null) use (&$next, &$stack, $req, $res, $done) {
			$stop = in_array($error, ['route', 'router']);
			if ($stop || empty($stack) || $res->ended) return $done($error);

			$handler = array_shift($stack);

			if (! $error ) $this->handleRequest($handler, $req, $res, $next);
			else $this->handleError($handler, $error, $req, $res, $next);
		};
		$next($error);
	}
}
