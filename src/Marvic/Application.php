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
 * This class represents an Marvic Application, used to manage app settings, routes and
 * subroutes, middleware functions, app events, view engines and app tree for request
 * processing and response delivering, either web or command-line interface.
 * 
 * @package Marvic
 */
final class Application {
	/**
	 * Allowed Application Event Tokens
	 */
	private const ALLOWED_EVENTS = ['start', 'finish', 'request', 'response', 'mount',
		'error'];

	/**
	 * The path where the application is mounted.
	 *
	 * @var string
	 */
	private string $mountpath;

	/**
	 * The Registed Application Events.
	 *
	 * @var array<string, Callable>
	 */
	private array $events = [];

	/**
	 * The Application Engines.
	 * 
	 * @var array<string, Callable|object>
	 */
	private array $engines = [];

	/**
	 * The Marvic Application Parent Instance.
	 *
	 * @var Marvic\Application
	 */
	public readonly self $parent;

	/**
	 * The Marvic Application Settings Instance.
	 * 
	 * @var Marvic\Settings
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
		$this->settings = new Settings([
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
		]);
		$this->settings->merge($settings->all());

		$this->router = new Router([
			'strict'        => $this->get('http.strict'),
			'mergeParams'   => $this->get('http.mergeParams'),
			'caseSensitive' => $this->get('http.caseSensitive'),
		]);
		$this->mountpath = $this->router->mountpath;
	}

	/**
	 * Return the string representation of Instance.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		return "<Application mount on '$this->mountpath'>";
	}

	/**
	 * Becomes allowed private properties as readonly.
	 *
	 * @param  string $name
	 * @return mixed
	 */
	public function __get(string $name): mixed {
		$allowed = ['mountpath'];
		if ( in_array($name, $allowed) ) return $this->$name;
		throw new Exception("Undefined property: $name");
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
	 * Mount the application parent and dispatch the 'mount' event.
	 *
	 * @param self $parent
	 */
	private function mount(self $parent): void {
		$this->parent = $parent;
		if ( isset($this->events['mount']) )
			call_user_func_array($this->events['mount'], [$parent]);
	}

	/**
	 * Bootstrap the application
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
	 * Add a new route with method GET if the first arguments is a string that starts
	 * with '/'. Else, get an application settings data.
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
	 * Mount a callable, instance method (in array), router or application as a
	 * middleware in a specifical route path.
	 * 
	 * @param array $arguments
	 */
	public function use(...$arguments): void {
		foreach ($arguments as $index => $middleware) {
			if ($middleware instanceof self) {
				$middleware->mount($this);
				$arguments[$index] = $middleware->router;
			}
		}
		$this->router->use(...$arguments);
		$this->mountpath = $this->router->mountpath;
	}

	/**
	 * Add a new handler function in an application event.
	 *
	 * Avaliable events:
	 *   error
	 *     - Dispatched when an application throws an internal server error.
	 *
	 *     Signature: function(mixed $error, Marvic\HTTP\Message\Response $response)
	 *
	 *   finish
	 *     - Dispatched when an application is finished.
	 *
	 *     Signature: function()
	 *
	 *   mount
	 *     - Dispatched when an application is mounted by your parent.
	 *
	 *     Signature: function(Marvic\Application $parent)
	 *
	 *   request
	 *     - Dispatched when an application request processing is started.
	 *
	 *     Signature: function(Marvic\HTTP\Message\Request $request)
	 *
	 *   response
	 *     - Dispatched when an application response is sent.
	 *
	 *     Signature: function(Marvic\HTTP\Message\Response $response)
	 *
	 *   start
	 *     - Dispatched when an application is started.
	 *
	 *     Signature: function()
	 *
	 * @param  string   $event
	 * @param  Callable $callback
	 */
	public function on(string $event, Callable $callback): void {
		if (! in_array($event, self::ALLOWED_EVENTS) ) {
			$message = "Unsupported application event: $event";
			throw new InvalidArgumentException($message);
		}
		$this->events[$event] = $callback;
	}

	/**
	 * Render a view template file (independent of the extension).
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
	 * Avaliable Engines:
	 *   view
	 *     - View wngine for template rendering.
	 *
	 *     Function signature: function(string $view, array $data = [])
	 * 
	 * @param  string          $name
	 * @param  object|Callable $engine
	 */
	public function engine(string $name, object $engine): void {
		$this->engines[$name] = $engine;
	}

	/**
	 * Handle an HTTP request and send a response.
	 *
	 * @param  Marvic\HTTP\Message\Request  $request
	 * @return Marvic\HTTP\Message\Response
	 */
	private function handleRequest(Request $request): Response {
		if ( isset($this->events['start']) )
			call_user_func_array($this->events['start'], []);

		if ( isset($this->events['request']) )
			call_user_func_array($this->events['request'], [$request]);

		$response = new Response($request);

		$done = function($error = null) use ($response) {
			if (  $response->ended ) return;

			if ( is_string($error) ) {
				$error = new RuntimeException($error);
			}
			if (! ($error instanceof Exception) ) return;

			if ( in_array('error', $this->events) ) {
				$errorHandler = $this->events['error'];
				$errorHandler($error, $response);
				return;
			}

			$response->format([
				'html' => function() use ($error, $response) {
					$message  = '<h1>500 Internal Server Error</h1>';
					$message .= '<code>';
					$message .= 'Error Code: '. (string)$error->getCode() .'<br>';
					$message .= 'Error Message: '. $error->getMessage() .'<br>';
					$message .= 'File: '. $error->getFile();
					$message .= ' in line '. (string)$error->getLine();

					$trace = str_replace("\n", '<br><br>', $error->getTraceAsString());
					$message .= "<br><br>TRACE:<br>$trace";

					$message .= '</code>';
					$response->sendStatus(500, $message);
				},
				'json' => function() use ($error, $response) {
					$response->sendJson([
						'errorCode' => $error->getCode(),
						'errorMessage' => $error->getMessage(),
						'inFile' => $error->getFile(),
						'inLine' => $error->getLine(),
						'trace' => explode("\n", $error->getTraceAsString()),
					]);
					$response->setStatus(500);
				},
				'default' => function() use ($error, $response) {
					$message  = "500 Internal Server Error:\n\n";
					$message .= 'Error Code: '. (string)$error->getCode() ."\n";
					$message .= 'Error Message: '. $error->getMessage() ."\n";
					$message .= 'File: '. $error->getFile();
					$message .= ' in line '. (string)$error->getLine();

					$trace = str_replace("\n", "\n\n", $error->getTraceAsString());
					$message .= "\n\nTRACE:\n$trace";

					$response->sendStatus(500, $message);
				},
			]);
		};

		$this->router->handle($request, $response, $done);
		if (! $response->ended ) {
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
		}
		return $response;
	}

	/**
	 * Run the application in web interface.
	 */
	public function run(): void {
		if ( defined('PHP_SAPI') && PHP_SAPI === 'cli' ) return;
		$this->bootstrap();
		
		$http     = new HttpKernel();
		$request  = $http->captureRequest($this);
		$response = $this->handleRequest($request);
		$http->send($response);

		if ( isset($this->events['response']) )
			call_user_func_array($this->events['response'], [$response]);

		if ( isset($this->events['finish']) )
			call_user_func_array($this->events['finish'], []);
	}

	/**
	 * Respond an application request from method and path.
	 *
	 * @param  string $method
	 * @param  string $path
	 * @param  array  $options
	 * @return Marvic\HTTP\Message\Response
	 */
	public function request(string $method, string $path, array $options = []): ?Response {
		if ( !defined('PHP_SAPI') || PHP_SAPI !== 'cli' ) return null;
		$this->bootstrap();

		$path     = $this->get('app.baseurl', "http://localhost") . $path;
		$http     = new HttpKernel();
		$request  = $http->newRequest($this, $method, $path, $options);
		$response = $this->handleRequest($request);

		if ( isset($this->events['response']) )
			call_user_func_array($this->events['response'], [$response]);

		if ( isset($this->events['finish']) )
			call_user_func_array($this->events['finish'], []);

		return $response;
	}
}
