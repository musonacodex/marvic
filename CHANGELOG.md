# Changelog

## [v1.3.1] - Unreleased

**Fixed**

- Double CRLF between headers and body of a HTTP message (either request and response).
- Fatal error of the assignment of Request::$route property.
- Function option handling of Marvic::static() static method.
- The undefined property error of Application::$mountpath instance property.

## [v1.3.0] - 2025-10-14

**Added**

- The `Router::set()` method to configure the router properties.

**Fixed**

- `Application::request()` method return a Response object now.
- `Request::route` property store the current route path as a string.
- Fatal errors when the 'http.xPoweredBy' app setting is enabled.
- Error handling to parse a request json using `Request::json()` method.
- Route parameter handling and your access using `Request::input()` method.
- The `$mountpath` property updating when either app or router are mounted by other apps or routers.

## [v1.2.1] - 2025-09-20

**Fixed**

- The "onError" application event handling.
- THe error sharing between routes, routers and sub-routers.
- Errors to send file response through 'sendFile', 'download' and 'stream' methods.
- Additions of default HTTP response headers before to send to client.
- The undefined variable errors on `Router::view()` and `Router::redirect()` instance methods.

## [v1.2.0] - 2025-09-17

**Added**

- The route mapper support using `Marvic\Routing\Router::map()` method.
- The Application events registration using `Marvic\Application::on()` method.
- The Application Local Variables using `Marvic\Application::$locals` property.
- The Controller Routing using `Marvic\Application::controller()` method.
- The onStart, onFinish, onRequest, onResponse, onError and onMount application events.
- The Default "500 Internal Server Error" page with error messages.

**Fixed**

- Extract and merge route parameters.
- Solve the access of `Marvic\Routing\Router::match()` method.
- Return of the router self for `Router::map()` and `Router::prefix()` method.

## [v1.1.2] - 2025-09-16

**Fixed**

- The handling of route handler callbacks between routes.
- The building and validation of view fullpath in `Marvic\Application::render()` method.

## [v1.1.1] - 2025-09-16

**Fixed**

- Application bootstraping when `Marvic\Application::run()` method is runned.
- Rename `Marvic\Application::test()` method to `request` according with API documentation.

## [v1.1.0] - 2025-09-04

**Added**

- The 'http.strict' as required application settings.
- The 'http.mergeParams' as required application settings.
- The 'http.caseSensitive' as required application settings.
- The 'test' method to Marvic\Application class.
- The 'is' method to Marvic\HTTP\Cookie\Collection class.
- Support of multiroutes in same path through 'route' method of Marvic Router class.
- Support of router mounting and multirouter through Marvic Appkucation or Router class.
- Support of template engines installation with fn(string $view, array $data = []) signature.

**Fixed**

- The raw string format of the HTTP messages.
- Fatal errors about route path and error handling on routing system.
- Problems about application engines, including template engines.
- The performance of 'get', 'set', 'has' and 'merge' methods of Marvic\Settings class.
- The Marvic\HTTP\Cookie\Collection::get() method return string or null instead of Marvic\HTTP\Cookie.

## [v1.0.0] - 2025-08-26
- Initial launch of framework.
