<?php

use Marvic\Settings;
use Marvic\Application;
use Marvic\HTTP\Client as HttpClient;
use Marvic\HTTP\MimeTypes;
use Marvic\Routing\Router;

/**
 * This is the top-level class that provides methods to create a marvic
 * application, middleware and other nstances.
 * 
 * @package Marvic
 * @version 1.0.0
 */
final class Marvic {
	/**
	 * The Marvic Framework Version.
	 */
	public const VERSION = '1.1.0';

	/**
	 * Get the default configuration.
	 * 
	 * @return array
	 */
	private static function defaultConfiguration(): array {
		return [
			'app' => [
				'name'        => 'marvic',
				'baseurl'     => 'http://127.0.0.1',
				'language'    => 'en-US',
				'environment' => 'development',
				'description' => 'A Marvic Web Application',
				'debug'       => true,
				'timezone'    => 'UTC',
			],
			'http' => [
				'proxy'           => true,
				'cache'           => false,
				'strict'          => false,
				'expiresAt'       => 3600,
				'xPoweredBy'      => true,
				'trustProxy'      => 'X-Forwarded-For',
				'mergeParams'     => false,
				'caseSensitive'   => false,
				'allowedOrigins'  => [],
				'allowedMethods'  => [],
				'allowedHeaders'  => [],
				'subdomainOffset' => 2,
			],
			'folders' => [
				'views'       => "./views",
				'static'      => "./static",
				'routes'      => "./routes",
				'models'      => "./models",
				'uploads'     => "./uploads",
				'services'    => "./services",
				'database'    => "./database",
				'controllers' => "./controllers",
				'middlewares' => "./middlewares",
			],
		];
	}

	/**
	 * Get the static serve middleware.
	 * 
	 * @param  string   $directory
	 * @param  array    $options
	 * @return Callable
	 */
	public static function static(array $options = []): Callable {
		return function($request, $response, $next) use ($options) {
			$directory = $request->app->get('folders.static');
			$file      = $directory . $request->path;
			
			if (! (file_exists($file) && is_file($file)) ) return $next();

			$filepath = substr($request->path, 1);
			$response->sendFile($filepath, ['basedir' => $directory]);
		};
	}

	/**
	 * Get a new HTTP client instance.
	 * 
	 * @return Marvic\HTTP\Client
	 */
	public static function httpClient(): HttpClient {
		return new HttpClient();
	}

	/**
	 * Get a new Router instance.
	 * 
	 * @return Marvic\Routing\Router
	 */
	public static function router(array $options = []): Router {
		return new Router($options);
	}

	/**
	 * Get a new Marvic Application Settings instance.
	 *
	 * @param  array $data
	 * @return Marvic\Core\Settings
	 */
	public static function settings(array $data = []): Settings {
		$data = array_merge(self::defaultConfiguration(), $data);
		return new Settings($data);
	}

	/**
	 * Get a new Marvic Application instance.
	 * 
	 * @param  array $data
	 * @return Marvic\Core\Application
	 */
	public static function application(array $data = []): Application {
		$settings = self::settings($data);
		return new Application($settings);
	}
}