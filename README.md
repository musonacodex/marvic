# Marvic - The Faster and Minimalist PHP Web Application Framework
------------------------------------------------------------------

## Table of Contents

## What is Marvic?

**Marvic** is a minimalist and flexible PHP web application framework with expressive and elegant syntax. This mini framewrok provides a set of features for building of a web application easily. It simplifies the server-side development or server-side application by offering an east to use API for routing, middlewares and HTTP utilities, view engines, and so on.

Marvic is based on [Express.js](https://expressjs.com/) and [Koa](https://koajs.com/), so if you have nearness with one of those framework, use Marvic will be easy for you.

## Marvic Application Examples

Building a Marvic application is very easy, you need do only 3 steps:

* Create a new Marvic application instance.
* Define all needed routes.
* Run the application.

See the example below:

```php
$app = Marvic::application();

$app->get('/', function($request, $response) {
	$response->send('<h1>Hello! Welcome to Marvic!</h1>');
});

$app->run();
```

## Features

* **Routing**: allows to define routes and handle HTTP requests for your web application or RESTful API.

```php
$app->get('/users', function($request, $response) {
	return 'List of users';
});
```

* **Middlewares**: You can install middleware functions to access request, response, error message and other middlewares. You can use it for user authentication, error handling, data processing and logging for example.

```php
$app->use(function($error, $request, $response, $next) {
	if ( $error !== 'notFound' ) return $next();
	$response->sendStatus(404, '<h1>404 Not Found</h1>');
});
```

* **View Engine Functions**: Use your favorite template engine for your project (BladeOne, Twig, Plates, and so on). With him, you cand render templates easily. The default template engine is Plates.

```php
$twig = new Twig\Environment(new Twig\Loader\FilesystemLoader('./view'));

$app->engine('view', fn($view, $data) => $twig->render($view, $data));

$app->get('/', function($request, $response) {
	$response->render('index.html', ['name' => 'John']);
});
```

## Why should I use?

Marvic is a simple PHP framework and can be quite useful for web application or API building. But the decision depends of the project size, your requirements, mantainment and level of control ehat you want to have. By while, Marvic is useful for:

* **Small or Middle Projects**: You don't need a robust framework as Laravel or Symfony, but you also don't want do "everything on the nail". You can use to build small APIs, landing pages, and so on.
* **Prototype or MVP (Minimum Viable Product)**: Allows to create fast routes, cope with requests/responses, organize folders, and have a weightless structure.
* **RESTful APIs or Specifical Services**: If your web application will be a backend for frontends (SPA, mobile, etc), simple frameworks like Marvic wont be ideals because performance and low complexity.
* **Learning**: If you are beginning and you want to understand how to work concepts like routing, controllers, middlewares HTTP requests and responses, etc, Marvic can help you.
* **When the performance is priority**: Big frameworks add overhead (extra layers and dependences). If the project need be fast, use Marvic can be better.

## Requeriments

* PHP 8.1 or higher.

## Get Started

## Documentation

See the API Documentation [Click Here](./docs/api/v1.x.md)

## License

[MIT](LICENSE)
