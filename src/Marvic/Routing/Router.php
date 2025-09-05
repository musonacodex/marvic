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
	 * The base URL path where the router is mount.
	 * 
	 * @var string
	 */
	private string $mountpath = '/';

	/**
	 * Router parent reference on an router tree.
	 * 
	 * @var self
	 */
	private readonly self $parent;

	/**
	 * All nested routes.
	 * 
	 * @var array
	 */
	private array $collection = [];

	/**
	 * THe prefix for routes with similar URL path.
	 * 
	 * @var string
	 */
	private string $prefix = '';

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
		return "<Router mount on '$this->mountpath'>";
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
			$message = "The route path must be a string";
			throw new InvalidArgumentException($message);
		}
		$path = array_shift($arguments);

		if (! in_array($name, array_map('strtolower', Methods::all())) ) {
			$message = "Invalid route method: $name";
			throw new InvalidArgumentException($message);
		}
		return $this->match([strtoupper($name)], $path, ...$arguments);
	}

	/**
	 * Return the URL path where the router is mount.
	 * 
	 * @return string
	 */
	public function mountpath(): string {
		return $this->mountpath;
	}

	/**
	 * Define the parent of this application.
	 * 
	 * @param Marvic\Routing\Router $router
	 */
	private function parent(self $router, string $path = ''): void {
		$this->parent = $router;
		if (! empty($path) ) $this->mountpath = $path;
	}

	/**
	 * Check if a callback is valid middleware.
	 * 
	 * @param Callable $handler
	 */
	private function validateHandler(Callable $handler): void { 
		$reflection = new ReflectionFunction($handler);
		if (! empty($reflection->getParameters()) ) return;
		$message = "Handler arguments is required";
		throw new InvalidArgumentException($message);
	}

	/**
	 * Add new route with multiple methods.
	 * 
	 * @param  array      $methods
	 * @param  string     $path
	 * @param  Callable[] $handlers
	 * @return Marvic\Routing\Router
	 */
	public function match(array $methods, string $path, ...$handlers): self {
		$path = $this->mountpath . $this->prefix . $path;
		$path = preg_replace('/\/\/+/', '/', $path);
		
		if (strlen($path) > 1 && str_ends_with($path, '/'))
			$path = substr($path, 0, -1);

		$matcher = new RouteMatcher($path, [
			'end'       => true,
			'strict'    => $this->strict,
			'sensitive' => $this->caseSensitive,
		]);
		foreach ($handlers as $handler) {
			$this->validateHandler($handler);
			foreach ($methods as $method) {
				$route = new Route($method, $path, $handler, $matcher);
				$this->collection[] = $route;
			}
		}
		return $this;
	}

	/**
	 * Add a new route with all route methods.
	 * 
	 * @param  string     $path
	 * @param  Callable[] $handlers
	 * @return Marvic\Routing\Router
	 */
	public function any(string $path, ...$handlers): self {
		return $this->match($path, Methods::all(), ...$handlers);
	}

	/**
	 * Add a new route used to render a view.
	 * 
	 * @param  string $path
	 * @param  string $name
	 * @param  array  $data
	 * @return Marvic\Routing\Router
	 */
	public function view(string $path, string $name, array $data = []): self {
		return $this->get($path, function($request, $response) use ($name, $data) {
			$response->render($name, $data);
		});
	}

	/**
	 * Add a new route used to redirect to another URL.
	 * 
	 * @param  string  $path
	 * @param  string  $uri
	 * @param  integer $status
	 * @return Marvic\Routing\Router
	 */
	public function redirect(string $path, string $uri, int $status = 302): self {
		return $this->get($path, function($request, $response) use ($uri, $status) {
			$response->redirect($uri, $status);
		});
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

		$path = ($this->mountpath === '/') ? '' : $this->mountpath;
		if ( is_string($arguments[0]) ) {
			$path .= $this->prefix . array_shift($arguments);
		}
		$matcher = new RouteMatcher($path, [
			'end'       => false,
			'strict'    => $this->strict,
			'sensitive' => $this->caseSensitive,
		]);

		if ( empty($arguments) ) {
			$message = "Argument middleware is required";
			throw new InvalidArgumentException($message);
		}
		foreach ($arguments as $middleware) {
			$callback = null;
			if ( is_callable($middleware) ) {
				$callback = $middleware;
			}
			else if ( is_array($middleware) ) {
				$callback = function($req, $res, $next) use ($middleware) {
					call_user_func_array($middleware, [$req, $res, $next]);
					$next();
				};
			}
			else if ($middleware instanceof Router) {
				$middleware->parent($this, $path);
				$callback = function($req, $res, $next) use ($middleware) {
					$middleware->handle($req, $res);
					$next();
				};
			}
			else {
				$message = "Invalid argument middleware";
				throw new InvalidArgumentException($message);
			}

			foreach (Methods::all() as $method) {
				$route = new Route($method, $path, $callback, $matcher);
				$this->collection[] = $route;
			}
		}
		return $this;
	}

	/**
	 * Find routes by method and real path.
	 * 
	 * @param  string $method
	 * @param  string $path
	 * @return array
	 */
	public function findRoutes(string $method, string $path = '/'): array {
		$callback = fn($route) =>
			$route->method === $method && $route->match($path);
		return array_filter($this->collection, $callback);
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
		$stack = $this->findRoutes($req->method, $req->path);

		if ( empty($stack) ) { $done(); return; }

		$next = function($error = null) use (&$next, &$stack, $done, $req, $res) {
			if (in_array($error, ['route', 'router']) || count($stack) <= 0) 
				return $done($error);

			$route = array_shift($stack);
			$req->route = $route;

			if ( $error ) $route->handleError($error, $req, $res, $next);
			else $route->handleRequest($req, $res, $next);
		};
		$next();
	}
}