<?php

namespace Marvic\Http\Route;

final class Matcher {
	public  readonly string $pattern;
	private readonly string $regex;
	
	private readonly bool $end;
	private readonly bool $strict;
	private readonly bool $sensitive;

	public function __construct(string $pattern, array $options = []) {
		$this->pattern   = $pattern;
		$this->end       = $options['end']       ?? true;
		$this->strict    = $options['strict']    ?? false;
		$this->sensitive = $options['sensitive'] ?? false;
		$this->regex     = $this->convertPathToRegex($pattern);
	}

	private function convertPathToRegex(string $path): string {
		$paramRegex = '([a-zA-Z0-9_-]+)\:?([a-zA-Z]*)';
		
		$requiredRegex = '/\{([a-zA-Z0-9_]+)(\:[a-zA-Z]+)?\}/U';
		$optionalRegex = '/\[([a-zA-Z0-9_]+)(\:[a-zA-Z]+)?\]/U';

		$requiredCallback = function($found) {
			print_r($found);
			$typeRegex = match ($found[2] ?? null) {
				':bool'  => 'true|false',
				':int'   => '[0-9]+',
				':float' => '[0-9]+\.?[0-9]*',
				':str'   => '[a-zA-Z0-9_-]+',
				default  => '[a-zA-Z0-9_-]+',
			};
			return "(?P<$found[1]>$typeRegex)";
		};
		$optionalCallback = function($found) {
			$typeRegex = match ($found[2]) {
				':bool'  => 'true|false',
				':int'   => '[0-9]*',
				':float' => '[0-9]*\.?[0-9]*',
				':str'   => '[a-zA-Z0-9_-]*',
				default  => '[a-zA-Z0-9_-]*',
			};
			return "(?P<$found[1]>$typeRegex)";
		};

		$path  = str_replace('/', '\/', $path);
		$path  = preg_replace_callback($optionalRegex, $optionalCallback, $path);
		$path  = preg_replace_callback($requiredRegex, $requiredCallback, $path);
		$path .= $this->strict ? '\/?' : '';
		$path .= $this->end    ? '$'   : '';

		return "/^$path/" . ($this->sensitive ? 'i' : '');
	}

	public function match(string $path): bool|array {
		if (! preg_match($this->regex, $path, $found) ) return false;
		$parameters = [];
		foreach ($found as $key => $value) {
			if (! is_string($key) ) continue;
			if ( $value === 'true' ) $value = true;
			if ( $value === 'false' ) $value = false;
			if ( is_numeric($value) ) $value = (float) $value;
			if ( $value === intval($value) ) $value = (int) $value;
			$parameters[$key] = $value;
		}
		return empty($parameters) ? true : $parameters;
	}

	public function format(array $arguments = []): string {
		$newUrl = $this->pattern;
		foreach ($arguments as $key => $value) {
			$newUrl = str_replace("{".$key."}", "$value", $newUrl);
			$newUrl = str_replace("[".$key."]", "$value", $newUrl);
		}
		return $newUrl;
	}
}
