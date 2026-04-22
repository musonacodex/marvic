# Marvic - The Minimalist, Powerful and Independent PHP Web Framework

```php
$app = Marvic::application();

$app->get('/helloworld', function($request, $response) {
    $response->send('Hello World!');
});

$app->run();
```
