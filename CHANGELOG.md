Changelog
=========

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
  `$response->sendStatus()` fatal errors. 

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
