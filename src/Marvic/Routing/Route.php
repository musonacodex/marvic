<?php

namespace Marvic\Routing;

use ReflectionFunction;
use Exception;
use RuntimeException;
use BadMethodCallException;
use InvalidArgumentException;

use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Request\Methods;
use Marvic\HTTP\Message\Response;
use Marvic\Routing\Route\Matcher;

/**
 * This class represents an application route, a path that defines how
 * an application responds to a request made to a given address (URL). 
 *
 * @package Marvic\Routing
 */
final class Route {
	/**
	 * @var string
	 */
	public readonly string $method;

	/**
	 * @var string
	 */
	public readonly string $path;

	/**
	 * @var Marvic\Routing\Route\Matcher
	 */
	public readonly Matcher $matcher;

	private readonly array $options;

	/**
	 * @var Callable
	 */
	private $handler;


	public function __construct(string $method, string $path, Callable $handler,
		array $options = [])
	{
		$this->path    = $path;
		$this->method  = $method;
		$this->handler = $handler;
		$this->options = $options;
	}

	public function match(string $path, string $prefix = ''): bool {
		$prefix  = $prefix === '/' ? '' : $prefix;
		$matcher = new Matcher($prefix . $this->path, $this->options);
		return $matcher->match($path);
	}

	public function extract(string $path, string $prefix = ''): array {
		$prefix  = $prefix === '/' ? '' : $prefix;
		$matcher = new Matcher($prefix . $this->path, $this->options);
		return $matcher->extract($path);
	}

	public function handleRequest(Request $req, Response $res, Callable $next): void {
		$callback   = $this->handler;
		$reflection = new ReflectionFunction($callback);
		$parameters = array_column($reflection->getParameters(), 'name');
		
		if ( count($parameters) > 3 ) { $next(); return; }

		try {
			$result = $callback($req, $res, $next);
			if (! is_null($result) ) $res->send($result);
			
		} catch (Exception $error) {
			$next($error);
		}
	}

	public function handleError(mixed $error, Request $req, Response $res,
		Callable $next): void
	{
		$callback   = $this->handler;
		$reflection = new ReflectionFunction($callback);
		$parameters = array_column($reflection->getParameters(), 'name');
		
		if ( count($parameters) !== 4 ) { $next($error); return; }

		try {
			$result = $callback($error, $req, $res, $next);
			if (! is_null($result) ) $res->send($result);
			
		} catch (Exception $otherError) {
			$next($otherError);
		}
	}
}
