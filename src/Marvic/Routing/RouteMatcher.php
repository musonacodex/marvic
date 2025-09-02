<?php

namespace Marvic\Routing;

/**
 * Marvic Route Matcher.
 *
 * This class is responsable to parse URL patterns with simple syntax
 * and extract named parameters. An URL pattern accepts required and
 * optional parameters. This class validate data type after the
 * extraction.
 *
 * Syntax:
 * - Required Parameters: {id}
 * - Optional Parameters: [slug]
 *
 * Examples:
 *
 * | URL Pattern           | URL path           | Parameters                            |
 * | --------------------- | ------------------ | ------------------------------------- |
 * | /user/{id}            | /user/123          | ['id' => 123]                         |
 * | /bloh/{year}[/{slug}] | /blog/2025/article | ['year' => 2025, 'slug' => 'article'] |
 *
 * @package Marvic\Routing
 */
final class RouteMatcher {
	/**
	 * The URL path pattern.
	 *  
	 * @var string
	 */
	public readonly string $pattern;

	/**
	 * The pattern regular expression representation.
	 *  
	 * @var string
	 */
	private string $regex;

	/** 
	 * Is the URL pattern a base URL?
	 * 
	 * @var boolean
	 */
	private bool $end;

	/** 
	 * May an URL path finish with a bar (/)?
	 * 
	 * @var boolean
	 */
	private bool $strict;

	/**
	 * Is the URL pattern a case sensitive? 
	 * 
	 * @var boolean
	 */
	private bool $sensitive;

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param string $pattern
	 * @param array  $options
	 */
	public function __construct(string $pattern, array $options = []) {
		$this->pattern   = $pattern;
		$this->end       = $options['end']       ?? true;
		$this->strict    = $options['strict']    ?? false;
		$this->sensitive = $options['sensitive'] ?? false;
		$this->regex     = $this->pathToRegex($pattern);
	}

	/**
	 * Build the regular expression from the URL pattern.
	 * 
	 * @param  string $path
	 * @return string
	 */
	private function pathToRegex(string $path): string {
		$path = str_replace('/', '\/', $path);
		
		$path = preg_replace_callback('/\{(.+)\}/U',
			fn($found) => "(?P<$found[1]>.+)", $path);
		
		$path = preg_replace_callback('/\[(.*)\]/U',
			fn($found) => "(?P<$found[1]>.*)", $path);

		if ( $this->strict ) $path .= '\/?';
		if ( $this->end    ) $path .= '$';
		
		return $this->sensitive ? "/^$path/i" : "/^$path/";
	}

	/**
	 * Check if the realURL is match against the pattern.
	 * 
	 * @param  string  $url
	 * @return boolean
	 */
	public function match(string $url): bool {
		return (bool) preg_match($this->regex, $url, $parameters);
	}

	/**
	 * Parse the real URL against the pattern and extract parameters.
	 * 
	 * @param  string $url
	 * @return array
	 */
	public function extract(string $url): array {
		preg_match($this->regex, $url, $parameters);
		foreach ($parameters as $key => $value) {
			if ( is_integer($key) ) { unset($parameters[$key]); continue; }
			if ( $value === 'true' ) $value = true;
			if ( $value === 'false' ) $value = false;
			if ( is_numeric($value) ) $value = (float) $value;
			if ( $value === intval($value) ) $value = (int) $value;
			$parameters[$key] = $value;
		}
		return $parameters;
	}

	/**
	 * Build a new real URL from parameters.
	 * 
	 * @param  array  $arguments
	 * @return string
	 */
	public function format(array $arguments = []): string {
		$newUrl = '';
		foreach ($arguments as $key => $value)
			$newUrl = str_replace(["\{$key\}","[$key]"], "$value" ?? '', $this->pattern);
		return $newUrl;
	}
}