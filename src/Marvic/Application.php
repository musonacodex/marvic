<?php

namespace Marvic;

use Exception;
use InvalidArgumentException;

use Marvic\Routing\Router;
use Marvic\HTTP\Kernel as HttpKernel;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Response;
use Marvic\HTTP\Message\Response\Status;

/**
 * Marvic Application Class
 *
 * This class represents an Marvic Web Application instance. An
 * Application can exists singly or to be inside of an application tree.
 * Every application is mount on a specifical URL path
 * 
 * @package Marvic\Core
 */
final class Application {
	/** 
	 * Base url path of application where it is mount.
	 * 
	 * @var string
	 */
	public string $mountpath = '/';

	/**
	 * @var array
	 */
	public array $context = [];

	/**
	 * Application parent reference on an application tree.
	 * 
	 * @var self
	 */
	public readonly ?self $parent;

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
		$this->settings = $settings;
		$this->router   = new Router();
	}

	/**
	 * Return the string representation of Instance.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		return "<Application mount on '$this->mountpath'>";
	}

	public function __call(string $name, array $arguments = []): mixed {
		$callback = $this->settings->get("engines.$name");
		if ( is_callable($callback) ) return $callback(...$arguments);
		return call_user_func_array([$this->router, $name], $arguments);
	}

	/**
	 * Define and set the application environment.
	 * 
	 * @param string $env
	 */
	private function defineEnvironment(string $env = 'development'): void {
		switch ( $env ) {
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
	 * Get an application settings data. If the settings key is not
	 * valid, add a new route with method GET.
	 * 
	 * @param  array $arguments
	 * @return mixed
	 */
	public function get(...$arguments): mixed {
		if ( preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*$/', $arguments[0]) )
			return $this->settings->get($arguments[0], $arguments[1] ?? null);
		return $this->__call('get', $arguments);
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
	 * Use a middleware and install it on an URL path using a router.
	 * If the argument contains an application, or group of them, they
	 * will be childs of this application.
	 * 
	 * @param array $arguments
	 */
	public function use(...$arguments): void {
		$this->router->use(...$arguments);
	}
	
	public function engine(string $name, object|Callable $engine): void {
		$this->settings->set("engines.$name", $engine);
	}

	/**
	 * Run the web application, pr finish if the type of intarface is CLI.
	 */
	public function run(?Callable $ondone = null): void {
		if ( defined('PHP_SAPI') && PHP_SAPI === 'cli' ) return;

		$env = $this->settings->get('app.environment', 'development');
		$this->defineEnvironment($env);

		$timezone = $this->settings->get('app.timezone', 'UTC');
		date_default_timezone_set($timezone);

		$routesDir = $this->settings->get('folders.routes', '');
		foreach (glob("$routesDir/*.web.php") as $file) {
			$newRouter = (fn($file) => (include $file))($file);
			$this->router->use( $newRouter );
		}

		$http     = new HttpKernel();
		$request  = $http->captureRequest($this);
		$response = $http->newResponse($request);

		$ondone = $ondone ?? function($error = null) {
			if ($error instanceof Exception) throw $error;
		};		
		$this->router->handle($request, $response, $ondone);

		if ( $response->ended ) { $http->send($response); return; }

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
		$http->send($response);
	}
}