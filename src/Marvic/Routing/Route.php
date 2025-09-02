<?php

namespace Marvic\Routing;

use Exception;
use ReflectionFunction;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Response;

/**
 * Marvic Application Route.
 * 
 * This class represents an application route, a path that defines how
 * an application responds to a request made to a given address (URL). 
 * A route store a path, method, handler and matcher (to parse URL
 * patterns).
 *
 * @package Marvic\Routing
 */
final class Route {
	/**
	 * A route method.
	 * 
	 * @var string
	 */
	public readonly string $method;

	/**
	 * A route path.
	 * 
	 * @var string
	 */
	public readonly string $path;

	/**
	 * A route handler.
	 * 
	 * @var Callable
	 */
	private $handler;

	/**
	 * A route matcher.
	 * 
	 * @var Marvic\Routing\RouteMatcher
	 */
	private readonly RouteMatcher $matcher;

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param string       $method
	 * @param string       $path
	 * @param Callable     $handler
	 * @param RouteMatcher $matcher
	 */
	public function __construct(string $method, string $path, Callable $handler,
		RouteMatcher $matcher)
	{
		$this->path    = $path;
		$this->method  = $method;
		$this->handler = $handler;
		$this->matcher = $matcher;
	}

	/**
	 * An alias method of RouteMatcher::match().
	 * 
	 * @param  string  $path
	 * @return boolean
	 */
	public function match(string $path): bool {
		return $this->matcher->match($path);
	}

	/**
	 * An alias method of RouteMatcher::extract().
	 * 
	 * @param  string $path
	 * @return array
	 */
	public function extract(string $path): array {
		return $this->matcher->extract($path);
	}

	/**
	 * Run the handler function to handle HTTP messages.
	 * 
	 * @param  Marvic\HTTP\Message\Request  $req
	 * @param  Marvic\HTTP\Message\Response $res
	 * @param  Callable                     $next
	 */
	public function handleRequest(Request $req, Response $res, Callable $next): void {
		$callback   = $this->handler;
		$reflection = new ReflectionFunction($callback);
		$parameters = array_column($reflection->getParameters(), 'name');
		
		if ( count($parameters) > 3 ) { $next(); return; }

		try {
			$result = call_user_func_array($callback, [$req, $res, $next]);
			if (! is_null($result) ) $res->send($result);
			
		} catch (Exception $error) {
			$next($error);
		}
	}

	/**
	 * Run the handler function to handle HTTP message errors.
	 * 
	 * @param  mixed                        $error
	 * @param  Marvic\HTTP\Message\Request  $req
	 * @param  Marvic\HTTP\Message\Response $res
	 * @param  Callable                     $next
	 */
	public function handleError(mixed $error, Request $req, Response $res,
		Callable $next): void
	{
		$callback   = $this->handler;
		$reflection = new ReflectionFunction($callback);
		$parameters = array_column($reflection->getParameters(), 'name');
		
		if ( count($parameters) !== 4 ) { $next($error); return; }

		try {
			$result = call_user_func_array($callback, [$error, $req, $res, $next]);
			if (! is_null($result) ) $res->send($result);
			
		} catch (Exception $otherError) {
			$next($otherError);
		}
	}
}
