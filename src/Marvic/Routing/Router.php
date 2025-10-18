<?php

namespace Marvic\Routing;

use ReflectionFunction;
use RuntimeException;
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
	 * The route controller that will be registered
	 *
	 * @var string
	 */
	private string $controller = '';

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
		$this->set($options);
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
	 * Becomes allowed private properties as readonly.
	 *
	 * @param  string $name
	 * @return mixed
	 */
	public function __get(string $name): mixed {
		$allowed = ['mountpath'];
		if ( in_array($name, $allowed) ) return $this->$name;
		throw new Exception("Undefined property: $name");
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
		$methods = ($name === 'match') ? array_shift($arguments) : $name;
		$path    = array_shift($arguments);

		if ($name === 'match') {
			if (! is_array($methods) ) {
				$message = "The first argument must be an array";
				throw new InvalidArgumentException($message);
			}
			if (! is_string($path) ) {
				$message = "The second argument must be a string";
				throw new InvalidArgumentException($message);
			}
		} else if (! is_string($path) ) {
			$message = "The first argument must be a string";
			throw new InvalidArgumentException($message);
		}

		if (! empty($this->controller) ) {
			foreach ($arguments as $index => $action) {
				if (! is_string($action) ) {
					$message = "Invalid controller action to route $this->controller";
					throw new RuntimeException($message);
				}
				if (! method_exists($this->controller, $action) ) {
					$message  = "Inexistent controller action: ";
					$message .= "$this->controller::$action()";
					throw new RuntimeException($message);
				}
				$arguments[$index] = [new $this->controller, $action];
			}
		}

		$route = $this->route($path);
		if ($name === 'match')
			$route->match($methods, ...$arguments);
		else
			$route->{$name}(...$arguments);
		return $this;
	}

	public function formatRoutePath(string $path): string {
		$path = preg_replace('/\/\/+/', '/', $path);
		return empty($path) ? '/' : $path;
	}

	/**
	 * Return the URL path where the router is mount.
	 * 
	 * @return string
	 */
	private function updateMountpath(): void {
		if ( $this->parent === null   ) return;
		if ( $this->mountpath === '/' ) return;
		$this->parent->updateMountpath();
		$this->mountpath = $this->parent->mountpath . $this->mountpath;
		$this->mountpath = $this->formatRoutePath($this->mountpath);
	}

	/**
	 * Define the parent of this application.
	 *
	 * @param Marvic\Routing\Router $router
	 */
	private function parent(self $router, string $path = ''): void {
		$this->parent = $router;
		$this->mountpath = $path;
		$this->updateMountpath();
	}

	public function set(array $options): void {
		if ( isset($options['strict']) )
			$this->strict = $options['strict'];

		if ( isset($options['mergeParams']) )
			$this->mergeParams = $options['mergeParams'];

		if (isset($options['caseSensitive']) )
			$this->caseSensitive = $options['caseSensitive'];
	}

	public function route(string $path): Route {
		$path = $this->formatRoutePath($this->prefix . $path);
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

		$path = is_string($arguments[0]) ? array_shift($arguments) : '/';
		$path = $this->formatRoutePath($this->prefix . $path);

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
					$handler->handle($req, $res, $next);
				};
				continue;
			}

			$message = "Invalid argument middleware";
			throw new InvalidArgumentException($message);
		}

		$matcher = new RouteMatcher($path, [
			'end'       => false,
			'strict'    => false,
			'sensitive' => $this->caseSensitive,
		]);
		$this->stack[] = $route = new Route($path, $matcher);
		$route->any(...$arguments);
		return $this;
	}

	/**
	 * Group routes with the same prefix.
	 *
	 * @param  string   $prefix
	 * @param  Callable $callback
	 */
	public function prefix(string $prefix, Callable $callback): self {
		$length = strlen($this->prefix);
		$this->prefix .= $prefix;
		$callback($this);
		$this->prefix = substr($this->prefix, 0, $length);
		return $this;
	}

	public function controller(string $classname, Callable $callback): self {
		if (! empty($this->controller) ) {
			$message = "A route cntroller is been used: $this->controller";
			throw new InvalidArgumentException($message);
		}
		if (! class_exists($classname) ) {
			$message = "Inexistent controller class name: $classname";
			throw new InvalidArgumentException($message);
		}
		$this->controller = $classname;
		$callback($this);
		$this->controller = '';
		return $this;
	}

	public function map(array $map): self {
		$callback = function(array $map, string $path = '/') use (&$callback) {
			foreach ($map as $key => $value) {
				if (! is_array($value) ) $value = [$value];

				if ( str_starts_with($key, '/') ) {
					$callback($value, $path . $key);
					continue;
				}
				call_user_func_array([$this, $key], [$path, ...$value]);
			}
		};
		$callback($map, empty($this->prefix) ? '/' : $this->prefix);
		return $this;
	}

	/**
	 * Find routes by real request method and path.
	 * 
	 * @param  string $method
	 * @param  string $path
	 * @return array
	 */
	private function findRoutes(Request $request): array {
		$path = $request->path;
		$matcher = new RouteMatcher($this->mountpath, [
			'end'       => false,
			'strict'    => false,
			'sensitive' => $this->caseSensitive,
		]);

		if ($this->mountpath !== '/' && $matcher->match($request->path)) {
			$parameters = $matcher->extract($request->path);
			$prefix     = $matcher->format($parameters);

			if ( str_starts_with($path, $prefix) ) {
				$path = mb_substr($path, strlen($prefix));
				$path = $this->formatRoutePath($path);
				$request->addParams($parameters, $this->mergeParams);
			}
		}

		$findRoutesByMethod = fn($route) => $route->handlesMethod($request->method);
		$findRoutesByPath   = fn($route) => $route->matcher->match($path);

		$routes = $this->stack;
		$routes = array_values(array_filter($routes, $findRoutesByMethod));
		$routes = array_values(array_filter($routes, $findRoutesByPath));
		return $routes;
	}

	/**
	 * Hanfle and dispatch all added routes.
	 * 
	 * @param  Marvic\HTTP\Message\Request  $req
	 * @param  Marvic\HTTP\Message\Response $res
	 * @param  Callable|null                $done
	 */
	public function handle(Request $req, Response $res, ?Callable $done = null): void {
		if ( $res->ended ) { $done($error); return; }

		$done  = $done ?? fn($error = null) => null;
		$stack = $this->findRoutes($req);

		if ( empty($stack) ) { $done(); return; }

		$next = function($error = null) use (&$next, &$stack, $done, $req, $res) {
			if ($error === 'router' || empty($stack) || $res->ended) return $done($error);

			$route = array_shift($stack);
			$req->route = $this->formatRoutePath($this->mountpath . $route->path);
			$route->dispatch($req, $res, $next, $error);
		};
		$next();
	}
}
