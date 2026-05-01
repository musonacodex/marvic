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

	public function __toString(): string {
		return "<Router mount in '$this->mountpath'>";
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

		$path = array_splice($arguments, $name === 'match' ? 1 : 0, 1)[0];
		if (! is_string($path) ) {
			$order   = $name === 'match' ? 'second' : 'first';
			$message = "The %s argument must be a string: %s::%s";
			$message = sprintf($message, $order, __CLASS__, $name);
			throw new InvalidArgumentException($message);
		}
		$route = $this->route($path);

		foreach ($arguments as $index => $handler) {			
			if (is_callable($handler)) {
				$route->{$name}($handler);
			}
			else if ($handler instanceof Router) {
				$route->{$name}(function($req, $res, $next) use ($handler) {
					$handler->handle($req, $res, $next);
				});
				$handler->mountParent($this, $path);
			}
			else {
				$message = "Invalid argument middleware";
				throw new InvalidArgumentException($message);
			}
		}
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
			var_dump("$path = $matcher->regex");
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
			if (is_callable($handler)) {
				$route->any($handler);
			}
			else if ($handler instanceof Router) {
				$route->any(function($req, $res, $next) use ($handler) {
					$handler->handle($req, $res, $next);
				});
				$handler->mountParent($this, $path);
			}
			else {
				$message = "Invalid argument middleware";
				throw new InvalidArgumentException($message);
			}
		}
		return $this;
	}

	public function handle(Request $req, Response $res, ?Callable $done = null): void {
		if ( $res->ended ) { $done($error); return; }

		$done  = $done ?? fn($error = null) => null;
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
