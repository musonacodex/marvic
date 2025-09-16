<?php

namespace Marvic;

use Exception;
use RuntimeException;
use InvalidArgumentException;

use Marvic\Routing\Router;
use Marvic\HTTP\Kernel as HttpKernel;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Response;
use Marvic\HTTP\Message\Response\Status;

/**
 * Marvic Application Class
 *
 * This class represents an Marvic Web Application instance.
 * 
 * @package Marvic\Core
 */
final class Application {
	/**
	 * The Application Engines.
	 * 
	 * @var array<string, Callable|object>
	 */
	private array $engines = [];

	/**
	 * The Marvic Application Settings Instance.
	 * 
	 * @var Marvic\Core\Settings
	 */
	public readonly Settings $settings;

	/**
	 * The Marvic Application Router Instance.
	 * 
	 * @var Marvic\Routing\Router
	 */
	public readonly Router $router;

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param Marvic\Settings $settings
	 */
	public function __construct(Settings $settings) {
		$defaultSettings = [
			'app' => [
				'name'     => 'marvic',
				'env'      => 'development',
				'debug'    => true,
				'baseurl'  => 'http://127.0.0.1',
				'language' => 'en-US',
				'timezone' => 'UTC',
			],
			'http' => [
				'cacheViews'      => false,
				'strict'          => false,
				'expiresAt'       => 3600,
				'xPoweredBy'      => true,
				'mergeParams'     => false,
				'caseSensitive'   => false,
				'allowedOrigins'  => [],
				'allowedMethods'  => [],
				'allowedHeaders'  => [],
				'subdomainOffset' => 2,
			],
			'folders' => [
				'views'   => "./views",
				'static'  => "./static",
				'routes'  => "./routes",
				'uploads' => "./uploads",
			],
		];
		$this->settings = $settings;
		$this->settings->merge($defaultSettings);

		$this->router   = new Router([
			'strict'        => $this->get('http.strict', false),
			'mergeParams'   => $this->get('http.mergeParams', false),
			'caseSensitive' => $this->get('http.caseSensitive', false),
		]);
	}

	/**
	 * Return the string representation of Instance.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		$path = $this->router->mountpath();
		return "<Application mount on '$path'>";
	}

	/**
	 * Add a new route with an HTTP method.
	 * 
	 * @param  string $name
	 * @param  array  $arguments
	 * @return mixed
	 */
	public function __call(string $name, array $arguments = []): mixed {
		return call_user_func_array([$this->router, $name], $arguments);
	}

	/**
	 * Define and set the application environment.
	 * 
	 * @param string $env
	 */
	private function bootstrap(): void {
		$timezone = $this->settings->get('app.timezone', 'UTC');
		date_default_timezone_set($timezone);

		switch ( $this->get('app.env', 'development') ) {
			case 'development':
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
				break;
				
			case 'production':
				ini_set('display_errors', 0);
				ini_set('display_startup_errors', 0);
				error_reporting(E_ALL && ~E_DEPRECATED);
				break;
				
			case 'testing':
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
				break;
		}
	}

	/**
	 * add a new route with method GET, or get an application settings
	 * data. if the route path don't starts with '/..
	 * 
	 * @param  array $arguments
	 * @return mixed
	 */
	public function get(...$arguments): mixed {
		if ( str_starts_with($arguments[0], '/') )
			return $this->router->get(...$arguments);
		return $this->settings->get(...$arguments);
	}

	/**
	 * Set an application settings data.
	 * 
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set(string $key, mixed $value): void {
		$this->settings->set($key, $value);
	}

	/**
	 * Use a callable function, instance method (in array), router
	 * instance or application instance as a middleware.
	 * 
	 * @param array $arguments
	 */
	public function use(...$arguments): void {
		foreach ($arguments as $index => $middleware) {
			if ($middleware instanceof self)
				$arguments[$index] = $middleware->router;
		}
		$this->router->use(...$arguments);
	}

	/**
	 * Render a template.
	 *
	 * @param  string $view
	 * @param  array  $data
	 * @return string
	 */
	public function render(string $view, array $data = []): string {
		$directory = $this->get('folders.views');

		$file = "$directory/$view";
		if (! (file_exists($file) && is_file($file)) ) {
			$message = "Inexistent view template file: $file";
			throw new InvalidArgumentException($message);
		}

		if ( isset($this->engine['view']) ) {
			$engine = $this->engine['view'];
		} else {
			$engine = function($file, $data = []) use ($directory) {
				$oldPaths = get_include_path();
				set_include_path($directory);
				extract($data);
				ob_start();
				include $file;
				$output = ob_get_clean();
				set_include_path($oldPaths);
				return $output;
			};
		}

		return $engine($file, $data);
	}
	
	/**
	 * Set an application engine (either object or callback).
	 * 
	 * @param  string          $name
	 * @param  object|Callable $engine
	 */
	public function engine(string $name, object $engine): void {
		if ( is_object($engine) || is_callable($engine) )
			$this->engines[$name] = $engine;
		throw new Exception("The engine '$name' must be object or callable");
	}

	/**
	 * Handle an HTTP request and send a response.
	 *
	 * @param  Marvic\HTTP\Message\Request  $request
	 * @return Marvic\HTTP\Message\Response
	 */
	private function handleRequest(Request $request): Response {
		$response = new Response($request);

		$this->router->handle($request, $response);
		if ( $response->ended ) return $response;

		$response->format([
			'html' => function() use ($response) {
				$response->sendStatus(404, "<h1>404 Not Found</h1>");
			},
			'json' => function() use ($response) {
				$response->setStatus(404);
				$response->sendJson([
					'status' => 404,
					'phrase' => Status::phrase(404),
				]);
			},
			'default' => function() use ($response) { 
				$response->sendStatus(404, "404 Not Found");
			},
		]);
		return $response;
	}

	/**
	 * Run the web application, pr finish if the type of intarface is CLI.
	 */
	public function run(): void {
		if ( defined('PHP_SAPI') && PHP_SAPI === 'cli' ) return;
		$this->bootstrap();
		
		$http     = new HttpKernel();
		$request  = $http->captureRequest($this);
		$response = $this->handleRequest($request);
		$http->send($response);
	}

	public function request(string $method, string $path, array $options = []): ?string {
		if ( !defined('PHP_SAPI') || PHP_SAPI !== 'cli' ) return null;
		$this->bootstrap();

		$path     = "http://testing$path";
		$http     = new HttpKernel();
		$request  = $http->newRequest($this, $method, $path, $options);
		$response = $this->handleRequest($request);
		return "$response";
	}
}
