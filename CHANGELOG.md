# Changelog

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
