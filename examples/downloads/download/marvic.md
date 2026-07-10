# Marvic - The Minimalist, Powerful and Independent PHP Web Framework

```php
$app = Marvic::application();

$app->get('/helloworld', function($request, $response) {
    $response->send('Hello World!');
});

$app->run();
```

## Features

- **Routing**: It allows to create static and dynamic routes based on router objects or route files, andsupports 9 HTTP request methods.

- **Middlewares**: They are callable functions that intercept a request before or after a handler/controller.

- **Imutable HTTP request**: It is an object with rich properties and limited useful methods, such as: parsed URL, important headers; content negotiation; getting inputs, queries and files; getting headers and cookies; and so on.

- **HTTP response builder**: It allows to acumulate headers, cookies and data for body before the sending. It support automatic Content-Type, output buffering, late shipping, and so on

- **Template Engines**: It allows to register template engine callbacks that follow the `function($view, $data)` signature. The engine is selected automatically based by the file extension.
