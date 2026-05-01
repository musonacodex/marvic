<?php

use Marvic\Http\Router;
use Marvic\Http\Message\Request;
use Marvic\Http\Message\Request\Methods;
use Marvic\Http\Message\Response;

final class Marvic {
	public static function router($options): Router {
		return new Router($options);
	}
}
