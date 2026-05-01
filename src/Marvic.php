<?php

use Marvic\Http\Router;
use Marvic\Http\Message\Request;
use Marvic\Http\Message\Request\Methods;
use Marvic\Http\Message\Response;

final class Marvic {
	public static function router($options): Router {
		return new Router($options);
	}

	public static function compress(array $options = []): Callable {
		return function(Request $request, Response $response, Callable $next) use ($options) {
			$level     = $options['level']     ?? -1;
			$threshold = $options['threshold'] ?? 1024; // 1KB

			$canCompress = true;
			$canCompress = $canCompress && !$response->has('Content-Encoding');
			$canCompress = $canCompress && $response->acceptsEncodings('gzip');
			$canCompress = $canCompress && $response->length >= $threshold;
			if (! $canCompress ) { $next(); return; }

			$compressed = gzencode($response->read(), $level);
			if ($compressed === false) { $next(); return; }

			$response->write($compressed);
			$response->set('Content-Encoding', 'gzip');
			$response->set('Vary', 'Accept-Encoding');
			$response->remove('Content-Length');
		};
	}
}
