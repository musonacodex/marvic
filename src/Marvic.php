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

	public static function cors(array $options = []): Callable {
		return function(Request $request, Response $response, Callable $next) use ($options) {
			$status      = $options['status']      ?? 204;
			$origins     = $options['origins']     ?? ['*'];
			$methods     = $options['methods']     ?? Methods::all();
			$headers     = $options['headers']     ?? ['Content-Type','Authorization','X-Requested-With'];
			$expiresAt   = $options['expiresAt']   ?? 3600; // 1 hour
			$credentials = $options['credentials'] ?? true;
			
			if (is_string($origins)) $origins = [$origins];

			$origin = $request->get('Origin', null);
			if ($origin && in_array($origin, $origins)) {
				$response->set('Access-Control-Allow-Origin', $origin);
				$response->set('Vary', 'Origin');
			}
			if ($request->method === Methods::OPTIONS) {
				$response->setStatus($status);
				$response->set('Access-Control-Allow-Methods', implode(', ', $methods));
				$response->set('Access-Control-Allow-Headers', implode(', ', $headers));
				$response->set('Access-Control-Max-Age', $expiresAt);
				$response->set('Access-Control-Allow-Credentials', "$credentials");
			} else {
				$response->set('Access-Control-Allow-Credentials', "$credentials");
				$next();
			}
		};
	}
}
