<?php

namespace Marvic\Http;

use RuntimeException;
use ReflectionFunction;
use InvalidArgumentException;
use Marvic\Http\Route;
use Marvic\Http\Route\Layer;
use Marvic\Http\Route\Matcher;
use Marvic\Http\Message\Request;
use Marvic\Http\Message\Request\Methods;
use Marvic\Http\Message\Response;

final class Router {
	private ?self  $parent    = null;
	private string $mountpath = '/';
	private array  $stack     = [];

	private bool $strict        = false;
	private bool $mergeParams   = false;
	private bool $caseSensitive = false;

	public function __construct(array $options = []) {
		$this->set($options);
	}

	public function __get(string $name): mixed {
		$allowed = ['mountpath'];
		if ( in_array($name, $allowed) ) return $this->$name;
		throw new Exception("Undefined property: $name");
	}

	public function __call(string $name, array $arguments): self {
		if ( empty($arguments) ) {
			$message = "Arguments are required: ". __CLASS__ ."::$name()";
			throw new InvalidArgumentException($message);
		}

		$routeArguments = [];
		$pathIndex = ($name === 'match') ? 1 : 0;

		if ($pathIndex === 1 && !is_array($arguments[0])) {
			$message = "First argument must be array: %s::%s";
			$message = sprintf($message, __CLASS__, $name);
			throw new InvalidArgumentException($message);
		}
		else if ($pathIndex === 1) {
			$routeArguments[] = array_shift($arguments);
		}
				
		if (! isset($arguments[$pathIndex]) ) {
			$message = "Path argument missing for {$name}()";
			throw new InvalidArgumentException($message);
		}
		if (! is_string($arguments[$pathIndex]) ) {
			$order   = $name === 'match' ? 'second' : 'first';
			$message = "The %s argument must be string: %s::%s()";
			$message = sprintf($message, $order, __CLASS__, $name);
			throw new InvalidArgumentException($message);
		}
		$path = array_shift($arguments);

		foreach ($arguments as $index => $handler) {			
			if (! ($handler instanceof self) ) continue;
			$arguments[$index] = function($req, $res, $next) use ($handler) {
				$handler->handle($req, $res, $next);
			};
			$handler->mountParent($this, $path);
		}

		array_push($routeArguments, ...$arguments);
		call_user_func_array([$this->route($path), $name], $routeArguments);
		return $this;
	}

	private function formatRoutePath(string $path): string {
		$path = preg_replace('/\/\/+/', '/', $path);
		return empty($path) ? '/' : $path;
	}

	private function mountParent(self $router, string $path): void {
		$this->parent = $router;
		$this->mountpath = $path;
		$this->updateMountpath();
	}

	private function updateMountpath(): void {
		if ( $this->parent === null   ) return;
		if ( $this->mountpath === '/' ) return;
		$this->parent->updateMountpath();
		$this->mountpath = $this->parent->mountpath . $this->mountpath;
		$this->mountpath = $this->formatRoutePath($this->mountpath);
	}

	private function findRoutes(Request $request): array {
		$layers = [];
		foreach ($this->stack as $route) {
			if (! $route->handlesMethod($request->method) ) continue;

			$path = $this->mountpath . $route->path;
			$path = $this->formatRoutePath($path);
			$matcher = new Matcher($path, [
				'end'       => !$route->middle,
				'strict'    => $this->strict,
				'sensitive' => $this->caseSensitive,
			]);
			if (! $matcher->match($request->path) ) continue;

			$layers[] = new Layer($route, $matcher);
		}
		return $layers;
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
		$path = $this->formatRoutePath($path);
		$this->stack[] = $route = new Route($path, false);
		return $route;
	}

	public function use(...$arguments): self {
		if ( empty($arguments) ) {
			$message = "Arguments are required";
			throw new InvalidArgumentException($message);
		}

		$path = is_string($arguments[0]) ? array_shift($arguments) : '/';
		$path = $this->formatRoutePath($path);

		if ( empty($arguments) ) {
			$message = "Argument middleware is required";
			throw new InvalidArgumentException($message);
		}

		$this->stack[] = $route = new Route($path, true);
		foreach ($arguments as $index => $handler) {			
			if ($handler instanceof Router) {
				$route->any(function($req, $res, $next) use ($handler) {
					$handler->handle($req, $res, $next);
				});
				$handler->mountParent($this, $path);
			}
			else {
				$route->any($handler);
			}
		}
		return $this;
	}

	public function handle(Request $req, Response $res, ?Callable $done = null): void {
		$done = $done ?? fn($error = null) => null;
		if ( $res->ended ) { $done(); return; }

		$stack = $this->findRoutes($req);

		$next = function($error = null) use (&$next, &$stack, $done, $req, $res) {
			if ($error === 'router' || empty($stack) || $res->ended)
				return $done($error);

			$layer = array_shift($stack);
			$req->applyRoute($layer, $this->mergeParams);
			$layer->route->dispatch($req, $res, $next, $error);
		};
		$next();
	}
}
