<?php

namespace Marvic\Http\Route;

use Marvic\Http\Route;

final class Layer {
	public readonly Route   $route;
	public readonly Matcher $matcher;

	public function __construct(Route $route, Matcher $matcher) {
		$this->route   = $route;
		$this->matcher = $matcher;
	}
}
