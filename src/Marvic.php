<?php

use Marvic\Settings;
use Marvic\Application;
use Marvic\HTTP\Client as HttpClient;
use Marvic\HTTP\MimeTypes;
use Marvic\HTTP\Message\Response\Status;
use Marvic\Routing\Router;

/**
 * This is the top-level class that provides methods to create a marvic
 * application, routers middleware and other instances.
 * 
 * @package Marvic
 */
final class Marvic {
	/**
	 * The Marvic Framework Version.
	 */
	public const VERSION = '1.3.0';

	/**
	 * Get the built-in middleware function that serves static files.
	 *
	 * Options:
	 *   root (string)
	 *     - Sets the root directory from which to serve static assets
	 *     - The default is the path defined by the application.
	 *
	 *   dotfiles (boolean or null)
	 *     - Determines how dotfiles are treated.
	 *     - 'true' value means not special treatment for dotfiles.
	 *     - 'false' value means deny request for a dotfile, respond 403 and call $next().
	 *     - 'null' value means dotfile doesn't exists, responds with 404 and call $next().
	 *
	 *   extensions (string[])
	 *     - A list of file extensions to search if the file is not found.
	 *
	 *   failthrough (boolean)
	 *     - If true, let client errors fail-through as unhndled requests.
	 *     - If false, forward a client error.
	 *
	 *   headers (array or Callable)
	 *     - A dictionary or function for setting response headers to serve with the file.
	 *
	 *     Function signature: function(string $path, Marvic\HTTP\Header\Collection $headers)
	 * 
	 * @param  array    $options
	 * @return Callable
	 */
	public static function static(array $options = []): Callable {
		return function($request, $response, $next) use ($options) {
			$app           = $request->app;
			$extensions    = $options['extensions']  ?? [];
			$dotfiles      = $options['dotfiles']    ?? null;
			$failthrough   = $options['failthrough'] ?? false;
			$headers       = $options['headers']     ?? null;
			$baseDirectory = $options['basedir'] ?? $app->get('folders.static', '');

			$file      = ltrim($request->path, '/');
			$fullpath  = "$baseDirectory/$file";
			$filename  = basename($fullpath);
			$directory = dirname($fullpath);
			$extension = "$directory/$filename";
			$extension = mb_substr($extension, strlen($extension));

			array_push($extensions, $extension ?? '');

			if (! is_dir($baseDirectory) ) {
				$message = "Inexistent root directory to serve static files: ";
				throw new InvalidArgumentException($message . $rootDirectory);
			}

			$files = array_map(fn($ext) => "$directory/$filename" . $ext, $extensions);
			$files = array_filter($files, fn($f) => file_exists($f) && is_file($f));

			if ( empty($files) ) {
				if (! $failthrough ) return $next();
				$response->setStatus(404);
				$response->write('404 Not Found');
				return $next();
			}

			$fullpath = $files[0];
			if ( str_starts_with($filename, '.') && $dotfiles !== true ) {
				$status = match ($dotfiles) { false => 403, null => 404 };

				if (! $failthrough ) {
					$response->setStatus($status);
					$response->write(Status::phrase($status));
				}
				return $next();
			}

			if ( is_array($headers) ) {
				foreach ($headers as $key => $value)
					$response->headers->set($key, $value);
			}
			else if ( is_callable($headers) ) {
				$headers($file, $response->headers);
			}
			else if (! is_null($headers) ) {
				$message = "The 'headers' option must be null, array or callaback";
				throw new InvalidArgumentException($message);
			}

			$file = ltrim(substr($fullpath, strlen($baseDirectory)), '/');
			$response->sendFile($file, ['basedir' => $baseDirectory]);
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
