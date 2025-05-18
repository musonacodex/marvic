# Marvic - The Faster and Minimalist PHP Web Application Framework
------------------------------------------------------------------

**Attention**: The Marvic Framework is under development. So it is recommended not to use this framework for now. 

```php
$app = Marvic\Marvic::newApplication();

$app->router->get('/', function($request, $response) {
	$response->send('Hello world! Welcome to Marvic!');
});

$app->run();
```

## Table of Contents

* [Installation](#Installation)
* [Features](#Features)
* [Requirements](#Requirements)
* [License](#License)

## Installation

This PHP library is avaliable through the composer. The installation is an easy process. But before installing, install PHP 7.2 or higher and composer in your machine.

```bash
php composer require marvic/marvic
```

For more informations about the PHP and composer installation:
* [PHP: Installation or Configuration - Manual](https://www.php.net/manual/en/install.php)
* [Download Composer](https://getcomposer.org/download/)

## Features

* **Routing**: allows to define routes and handle HTTP requests with methods such as GET, POST, PUT, and more.

```php
$app->router->get('/users', function($request, $response) {
	$response->send('List of users');
});
```

* **Middleware**: You can install middleware functions with access to Request, Response and other middleware objects. It can be used for authentication, error handling, data processing and logging.

```php
$app->use(function($error, $request, $response, $next) {
	if ($error === 'notFound') {
		$response->setStatus(404);
		$response->send('404 Not Found');
	}
});
```

* **View Engine Function Support**: Use your favorite view engine installing a view engine function. With  him, you can render dinamic HTML with any view engine such as Blade, Twig, Plates, and others.

```php
$loader = Twig\Loader\FilesystemLoader('templates/');
$twig   = Twig\Environment($loader);

$app->engine('view', fn($view, $data) => $twig->render($view, $data));
```

or

```php
$loader = Twig\Loader\FilesystemLoader('templates/');
$twig   = Twig\Environment($loader);

$app->engine('view', [$twig, 'render']);
```

## Requeriments

* PHP 7.2 or higher.

## License

[MIT](LICENSE)