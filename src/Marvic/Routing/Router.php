<?php

namespace Marvic\Routing;

use InvalidArgumentException;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Response;
use Marvic\Routing\Route\Collection as RouteCollection;

final class Router {
	/** 
	 * @var string
	 */
	public string $mountpath = '/';

	/**
	 * @var array
	 */
	private array $routes = [];

	/**
	 * @var string
	 */
	private string $prefix = '';

	/**
	 * @var boolean
	 */
	private bool $strict;

	/**
	 * @var boolean
	 */
	private bool $caseSensitive;


	function __construct(array $options = []) {
		$this->strict        = $options['strict']        ?? false;
		$this->caseSensitive = $options['caseSensitive'] ?? false;
	}

	public function __call(string $name, array $arguments): self {
		if ( empty($arguments) ) {
			$message = "Arguments are required";
			throw new InvalidArgumentException($message);
		}
		if ( !is_string($arguments[0]) ) {
			$message = "The route path must be a string";
			throw new InvalidArgumentException($message);
		}
		$path = array_shift($arguments);
		$this->route($path)->$name(...$arguments);
		return $this;
	}

	public function route(string $path): RouteCollection {
		if ($this->prefix && $path === '/') $path = '';
		$route = new RouteCollection($this->prefix . $path);
		array_push($this->routes, $route);
		return $route;
	}

	public function prefix(string $prefix, Callable $callback): void {
		$this->prefix = $prefix;
		$callback($this);
		$this->prefix = '';
	}

	public function use(...$arguments): self {
		$path = '/';
		if ( empty($arguments) ) {
			$message = "Arguments are required";
			throw new InvalidArgumentException($message);
		}
		if ( is_string($arguments[0]) ){
			$path = $this->prefix . array_shift($arguments);
		}
		if ( empty($arguments) ) {
			$message = "Argument middleware is required";
			throw new InvalidArgumentException($message);
		}

		foreach ($arguments as $middleware) {
			if ( is_callable($middleware) ) {
				$this->route($path)->use( $middleware );
			}
			else if ($middleware instanceof Router) {
				$middleware->mountpath = $path;
				$cb = function($req, $res, $next) use ($middleware, $path) {
					$middleware->handle($req, $res);
					$next();
				};
				$this->route($path)->use($cb);
			}
			else {
				$message = "Invalid argument middleware";
				throw new InvalidArgumentException($message);
			}
		}
		return $this;
	}

	public function handle(Request $req, Response $res, ?Callable $done = null): void {
		$method   = $req->method;
		$path     = $req->path;
		$done     = $done ?? fn($error = null) => null;
		$callback = fn($routes) => $routes->findRoutes($method, $path, $this->mountpath);
		$stack    = array_merge(...array_map($callback, $this->routes));

		if ( empty($stack) ) { $done(); return; }

		$next = function($error = null) use (&$next, &$stack, $done, $req, $res) {
			if (in_array($error, ['route', 'router']) || count($stack) <= 0) 
				return $done($error);

			$route = array_shift($stack);
			$req->route = $route;

			if ( $error )
				$route->handleError($error, $req, $res, $next);
			else
				$route->handleRequest($req, $res, $next);
		};
		$next();
	}
}