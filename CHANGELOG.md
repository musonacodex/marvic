Changelog
=========

[v0.6.0] - 2026-07-17
---------------------

**Added:**

- PHP named parameters support for http message instance methods.
- File etensions support for `Request::accepts()` instance method.

**Fixed:**

- File path validation for file response.
- The `http.mergeParams` application setting.
- The `maxAge` cookie parameter into `Response::setCookie()` instance method.

[v0.5.0] - 2026-07-02
---------------------

**Added:**

- PHP 8.2 as minimal requirement to use this framework.
- Marvic::static() middleware function.
- `null` value handling for no content responses.
- 'root' and 'dotfiles' options into sendFile(), download() and stream() instance methods.

**Fixed:**

- Debugging instructions and syntax errors.
- Router object properties from mutable to readonly.

[v0.4.1] - 2026-05-12
---------------------

**Fixed:**

- The effect of `app.folders.views` app setting.
- Internal fatal errors to use request instances.
- The http.subdomainOffset default value from false to 2.
- The magic method handlering into app and router instances.
- Accessibility of `$app->view()` and `$app->redirect()` instance methods.
- The `array(<class>, <method>)` validation format as a route handler.
- The use of `http.strictRoute`, `http.caseSensitive`and `http.mergeParams` app setting before to configure built-in app router.

[v0.4.0] - 2026-05-04
---------------------

**Added:**

- Support of array `[<instance>, <method>]` as a callable for routing.
- `$request->app` instance property to access the application.
- `$response->render()` instance method to send a view response.
- `$response->sendFile()` instance method to send a file response.
- `$response->download()` instance method to send a file download response.
- `$response->stream()` instance method to send a file stream response.
- `$app->view()` and `$router->view()` instance method to route view responses.
- `$app->redirect()` and `$router->redirect()` instance method to route redirect responses.
- `$app->render()` instance method to render view files.
- `$app->egine()` instance method to register template engines.

**Fixed:**

- Fatal errors of `$response->send()` instance method, when your argument is not supported.

[v0.3.0] - 2026-05-02
---------------------

**Added**:

- Application class to control settings, built-in router and app tree.
- `Marvic::application()` factory method to create marvic apps.

**Fixed**:

- The response transport to the client.
- The real client IP from`$request->ip`.
- Debugging messages when it's runned router instances.

[v0.2.0] - 2026-05-01
---------------------

**Added**:

- HTTP routing system support.
- `$request->route` instance property.
- `$request->params()` instance method.
- `Marvic::router()` factory method to create routers.
- `Marvic::compress()` middleware function for gzip compression.
- `Marvic::cors()` middleware function for cross origin request sharing.

**Fixed**:

- `strict` configuration treatment of a router instance.
- `$response->ended` instance property as readonly.
- `$response->sendStatus()` fatal errors. 

[v0.1.0] - 2026-04-28
---------------------

**Added:**

- Immutable HTTP request object.
- HTTP response encapsulator object.
- Message instance methods for content negotiation.
- Request Input and Files.
- HTML and JSON response support.

**Fixed:**

- `composer.json` parsing errors.

[v0.0.1] - 2026-04-22
---------------------

- Initial release.
