<?php

namespace Marvic\Routing\Route;

use Marvic\HTTP\Request;
use Marvic\Routing\Route;

/**
 * This class search the appropriated route that responds the current
 * HTTP request. The goal is to find this route and extract parameters
 * from the URL of the current HTTP request, using the route URL
 * pattern.
 *
 * @package Marvic\Routing
 */
final class Matcher {
	/** @var string */
	public readonly string $pattern;

	/** @var string */
	private string $regex;

	/** @var array */
	private array $parameters;

	public function __construct(string $pattern, array $options = []) {
		$this->pattern = $pattern;
		$this->regex   = $this->pathToRegex($pattern);

		if ( isset($options['end']) && $options['end'] === true )
			$this->regex = $this->regex . '$';
	}

	private function pathToRegex(string $path): string {
		$path = str_replace('/', '\/', $path);
		
		$path = preg_replace_callback('/\{(.+)\}/U',
			fn($found) => "(?P<$found[1]>.+)", $path);
		
		$path = preg_replace_callback('/\[(.*)\]/U',
			fn($found) => "(?P<$found[1]>.*)", $path);

		return "^$path";
	}

	public function match(string $url): bool {
		return (bool) preg_match("#$this->regex#", $url, $parameters);
	}

	public function extract(string $url): array {
		preg_match("#$this->regex#", $url, $parameters);
		
		foreach ($parameters as $key => $value) {
			if ( is_integer($key) ) unset($parameters[$key]);
			if ( is_numeric($value) ) $value = (float) $value;
			if ( $value === intval($value) ) $value = (int) $value;

			$parameters[$key] = $value;
		}
		return $parameters;
	}

	public function format(array $arguments = []): string {
		$newUrl = '';
		foreach ($arguments as $key => $value)
			str_replace(["<$key>","[$key]"], "$value", $this->pattern);
		return $newUrl;
	}
}