<?php

namespace Marvic\Routing;

use ReflectionFunction;
use InvalidArgumentException;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Request\Methods;
use Marvic\HTTP\Message\Response;

/**
 * Marvic Application Router Class.
 *
 * This class is responsable to manage all routes of a Marvic web
 * application. A router is mount on a specifical base URL path.
 * Routers can be mount on a tree structure.
 *
 * @package Marvic\Routing\Router
 */
final class Router {
	/**
	 * Router parent reference on an router tree.
	 * 
	 * @var self
	 */
	private ?self $parent = null;

	/** 
	 * The base URL path where the router is mount.
	 * 
	 * @var string
	 */
	private string $mountpath = '/';

	/**
	 * The route path prefix
	 * 
	 * @var string
	 */
	private string $prefix = '';

	/**
	 * List of route layer stack.
	 * 
	 * @var array
	 */
	private array $stack = [];

	/**
	 * @var bool
	 */
	private bool $strict = false;

	/**
	 * @var bool
	 */
	private bool $mergeParams = false;

	/**
	 * Is URL path case sensitive?
	 * 
	 * @var bool
	 */
	private bool $caseSensitive = false;

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param array $options
	 */
	public function __construct(array $options = []) {
		$this->strict        = $options['strict']        ?? false;
		$this->mergeParams   = $options['mergeParams']   ?? false;
		$this->caseSensitive = $options['caseSensitive'] ?? false;
	}

	/**
	 * Return the string representation of Instance.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		$path = $this->mountpath();
		return "<Router mount on '$path'>";
	}

	/**
	 * Add a new route with one of allowed HTTP methods.
	 * 
	 * @param  string $name
	 * @param  array  $arguments
	 * @return Marvic\Routing\Router
	 */
	public function __call(string $name, array $arguments): self {
		if ( empty($arguments) ) {
			$message = "Arguments are required";
			throw new InvalidArgumentException($message);
		}
		if ( !is_string($arguments[0]) ) {
			$message = "The first argument must be a string";
			throw new InvalidArgumentException($message);
		}
		$path = array_shift($arguments);
		$this->route($path)->{$name}(...$arguments);
		return $this;
	}

	/**
	 * Return the URL path where the router is mount.
	 * 
	 * @return string
	 */
	public function mountpath(): string {
		if ($this->parent === null && $this->mountpath === '/') return '';
		if ($this->parent === null) return $this->mountpath;
		return $this->parent->mountpath() . $this->mountpath;
	}

	/**
	 * Define the parent of this application.
	 * 
	 * @param Marvic\Routing\Router $router
	 */
	private function parent(self $router, string $path = ''): void {
		$this->parent = $router;
		$this->mountpath = $path;
	}

	/**
	 * Group routes with the same prefix.
	 * 
	 * @param  string   $prefix
	 * @param  Callable $callback
	 */
	public function prefix(string $prefix, Callable $callback): void {
		$length = strlen($this->prefix);
		$this->prefix .= $prefix;
		$callback($this);
		$this->prefix = substr($this->prefix, 0, $length);
	}

	public function route(string $path): Route {
		$path = $this->prefix . $path;
		$matcher = new RouteMatcher($path, [
			'end' => true,
			'strict' => $this->strict,
			'sensitive' => $this->caseSensitive,
		]);
		$this->stack[] = $route = new Route($path, $matcher);
		return $route;
	}

	/**
	 * Use callable functions, arrays and Routers as middlewares.
	 * 
	 * @param array $arguments
	 */
	public function use(...$arguments): self {
		if ( empty($arguments) ) {
			$message = "Arguments are required";
			throw new InvalidArgumentException($message);
		}

		$path = is_string($arguments[0]) ? array_shift($arguments) : '';
		$path = $this->prefix . $path ?? '/';
		
		if ( empty($arguments) ) {
			$message = "Argument middleware is required";
			throw new InvalidArgumentException($message);
		}

		foreach ($arguments as $index => $handler) {			
			if ( is_callable($handler) ) continue;

			if ( is_array($handler) ) {
				$arguments[$index] = function($req, $res, $next) use ($handler) {
					call_user_func_array($handler, [$req, $res, $next]);
					$next();
				};
				continue;
			}
			if ($handler instanceof Router) {
				$handler->parent($this, $path);
				$arguments[$index] = function($req, $res, $next) use ($handler) {
					$handler->handle($req, $res);
					$next();
				};
				continue;
			}

			$message = "Invalid argument middleware";
			throw new InvalidArgumentException($message);
		}

		$matcher = new RouteMatcher($path, [
			'end' => false,
			'strict' => false,
			'sensitive' => $this->caseSensitive,
		]);
		$this->stack[] = $route = new Route($path, $matcher);
		$route->any(...$arguments);
		return $this;
	}

	/**
	 * Find routes by real request method and  path.
	 * 
	 * @param  string $method
	 * @param  string $path
	 * @return array
	 */
	private function findRoutes(Request $request): array {
		$callback = fn($route) => $route->handlesMethod($request->method);
		$routes   = array_values(array_filter($this->stack, $callback));
		if ( empty($routes) ) return [];

		$callback = fn($route) => $route->matcher->match($request->path);
		$_routes  = array_values(array_filter($routes, $callback));
		if (! empty($_routes) ) return $_routes;

		$matcher = new RouteMatcher($this->mountpath(), [
			'end' => false, 'strict' => false,
			'sensitive' => $this->caseSensitive,
		]);
		if (! $matcher->match($request->path) ) return [];
		
		$path = '';
		$parameters = $matcher->extract($request->path);
		if ( $parameters ) {
			$prefix = $matcher->format($parameters);
			$path   = substr($request->path, strlen($prefix));
		} else {
			$path = substr($request->path, strlen($this->mountpath));
		}
		if ( empty($path) ) $path = '/';

		$callback = fn($route) => $route->matcher->match($path);
		return array_values(array_filter($routes, $callback)) ?? [];
	}

	/**
	 * Hanfle and dispatch all added routes.
	 * 
	 * @param  Marvic\HTTP\Message\Request  $req
	 * @param  Marvic\HTTP\Message\Response $res
	 * @param  Callable|null                $done
	 */
	public function handle(Request $req, Response $res, ?Callable $done = null): void {
		$done  = $done ?? fn($error = null) => null;
		$stack = $this->findRoutes($req);

		if ( empty($stack) ) { $done(); return; }

		$next = function($error = null) use (&$next, &$stack, $done, $req, $res) {
			if ( $error && $error === 'router' ) return $done();
			if ( empty($stack) ) return $done($error);

			$route = array_shift($stack);
			$route->dispatch($req, $res, $next);
		};
		$next();
	}
}