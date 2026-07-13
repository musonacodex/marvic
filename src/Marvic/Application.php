<?php

namespace Marvic;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Marvic\View\EngineManager;
use Marvic\Http\Router;
use Marvic\Http\Message\Transport;
use Marvic\Http\Message\Request;
use Marvic\Http\Message\Request\Uri;
use Marvic\Http\Message\Request\Methods;
use Marvic\Http\Message\Response;
use Marvic\Http\Message\Response\Status;
use Marvic\Http\Message\Header\Collection as Headers;
use Marvic\Http\Message\Cookie\Collection as Cookies;

final class Application {
	private ?self   $parent = null;
	private ?Router $router = null;

	private Settings      $settings;
	private EngineManager $engines;

	public function __construct(array $settings = []) {
		$this->settings = new Settings();
		$this->setDefaultConfiguration();
		$this->settings->merge($settings);

		$this->engines = new EngineManager();
	}

	public function __get(string $name): mixed {
		if ($name === 'mountpath') return $this->router->mountpath;
		
		$message = "Undefined instance property: %s::%s";
		$message = sprintf($message, __CLASS__, $name);
		throw new RuntimeException($message);
	}

	public function __call(string $name, array $arguments = []): mixed {
		$allowed = ['get','set','has','enable','disable','enabled','disabled'];
		if (method_exists(Settings::class, $name) && in_array($name, $allowed)) {
			if ($name !== 'get' || !str_starts_with($arguments[0], '/'))
				return call_user_func_array([$this->settings, $name], $arguments);
		}
		return $this->callRouterMethod($name, $arguments);
	}

	private function createNewRouter(): void {
		if ($this->router !== null) return;
		$this->router = new Router([
			'strict'        => $this->settings->get('http.strictRoute'),
			'mergeParams'   => $this->settings->get('http.mergeParams'),
			'caseSensitive' => $this->settings->get('http.caseSensitive'),
		]);
	}

	private function callRouterMethod(string $name, array $arguments): mixed {
		$allowed = ['any','match','view','redirect','use','route'];
		array_push($allowed, ...array_map('strtolower', Methods::all()));

		$message = "Undefined instance method: %s::%s()";
		$message = sprintf($message, __CLASS__, $name);
		in_array($name, $allowed) || throw new RuntimeException($message);

		foreach ($arguments as $index => $middleware) {
			if ($middleware instanceof self) {
				$middleware->mount($this);
				$middleware = $middleware->router;
			}
			if (is_string($middleware) && !str_starts_with($middleware, '/')) {
				$file = $this->settings->get('app.folders.routes');
				$file = preg_replace('/\/\/+/', '',  "$file/$middleware");
				if (! file_exists($file) ) {
					$message = "Route file does not exists: $middleware";
					throw new InvalidArgumentException($message);
				}
				$middleware = function($req, $res, $next) use ($file) {
					$router = (include $file);
					$router->handle($req, $res, $next);
				};
			}
			$arguments[$index] = $middleware;
		}
		$this->createNewRouter();
		$result = call_user_func_array([$this->router, $name], $arguments);
		return ($result instanceof Route) ? $result : $this;
	}

	private function mount(self $parent): void {
		$this->parent = $parent;
	}

	private function setDefaultConfiguration(): void {
		$this->settings->set('app.name',     'marvic');
		$this->settings->set('app.env',      'development');
		$this->settings->set('app.baseurl',  'https://localhost');
		$this->settings->set('app.language', 'en-US');
		$this->settings->set('app.timezone', 'UTC');

		$this->settings->set('app.folders.views',   './views');
		$this->settings->set('app.folders.static',  './static');
		$this->settings->set('app.folders.routes',  './routes');
		$this->settings->set('app.folders.uploads', './uploads');
	
		$this->settings->disable('http.strictRoute');
		$this->settings->disable('http.mergeParams');
		$this->settings->disable('http.caseSensitive');

		$this->settings->enable('http.xPoweredBy');
		$this->settings->disable('http.cacheViews');
		
		$this->settings->set('http.maxAge', 3600); // 1 hour
		$this->settings->set('http.subdomainOffset', 2);
	}

	private function bootstrap(): void {
		switch ($this->settings->get('app.env', 'development')) {
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

			default:
				$message = "Unsupported application environment: $environment";
				throw new RuntimeException($message);
		}
		
		$timezone = $this->settings->get('app.timezone', 'UTC');
		date_default_timezone_set($timezone);

		$this->createNewRouter();
	}

	public function engine(string|array $extensions, mixed $engine): void {
		$this->engines->register($extensions, $engine);
	}

	public function render(string $view, array $data = []): string {
		$view = $this->settings->get('app.folders.views', '/views') . "/$view";
		return $this->engines->render($view, $data);
	}

	private function handleRequest(Request $request): Response {
		$response = new Response($request);

		$done = function($error = null) use ($response) {
			if ( is_string($error) ) $error = new RuntimeException($error);
			if ($response->ended || !($error instanceof Exception)) return;
					
			$trace = str_replace("\n", "\n\n", $error->getTraceAsString());
			
			$message  = "500 Internal Server Error:<br>";
			$message .= 'Error Code: '. (string)$error->getCode() ."<br>";
			$message .= 'Error Message: '. $error->getMessage() ."<br>";
			$message .= 'File: '. $error->getFile();
			$message .= ' in line '. (string)$error->getLine();
			$message .= "<br><br>TRACE:<br>$trace";
			
			$response->sendStatus(500, $message);
		};

		$this->router->handle($request, $response, $done);
		if (! $response->ended ) {
			$response->format([
				'html' => function() use ($response) {
					$response->sendStatus(404);
				},
				'json' => function() use ($response) {
					$response->setStatus(404);
					$response->sendJson(['error' => Status::phrase(404)]);
				},
				'default' => function() use ($response) {
					$response->sendStatus(404, "404 Not Found");
				},
			]);
		}
		return $response;
	}

	public function run(): void {
		if ( defined('PHP_SAPI') && PHP_SAPI === 'cli' ) return;
		$this->bootstrap();
		
		$request  = Request::fromGlobals($this);
		$response = $this->handleRequest($request);
		Transport::send($response);
	}

	public function request(string $method, string $path, ...$options): ?Response {
		if ( !defined('PHP_SAPI') || PHP_SAPI !== 'cli' ) return null;
		$this->bootstrap();

		$uri = $this->settings->get('app.baseurl', "http://localhost");
		$uri = new Uri($uri . $path);

		$options['body']    = $options['body']    ?? '';
		$options['version'] = $options['version'] ?? '1.1';
		$options['headers'] = new Headers($options['headers'] ?? []);
		$options['cookies'] = new Cookies($options['cookies'] ?? []);

		if (! $options['headers']->has('Host') ) {
			$options['headers']->set('Host', $uri->authority);
		}
		if (! $options['headers']->has('Connection') ) {
			$options['headers']->set('Connection', 'close');
		}
		if (! $options['headers']->has('Cache-Control') ) {
			$options['headers']->set('Cache-Control', 'no-cache');
		}
		
		$request = new Request($this, $method, $uri, $options);
		return $this->handleRequest($request);
	}
}
