# phpnomad/fastroute-rest-integration

[![Latest Version](https://img.shields.io/packagist/v/phpnomad/fastroute-rest-integration.svg)](https://packagist.org/packages/phpnomad/fastroute-rest-integration)
[![Total Downloads](https://img.shields.io/packagist/dt/phpnomad/fastroute-rest-integration.svg)](https://packagist.org/packages/phpnomad/fastroute-rest-integration)
[![PHP Version](https://img.shields.io/packagist/php-v/phpnomad/fastroute-rest-integration.svg)](https://packagist.org/packages/phpnomad/fastroute-rest-integration)
[![License](https://img.shields.io/packagist/l/phpnomad/fastroute-rest-integration.svg)](https://packagist.org/packages/phpnomad/fastroute-rest-integration)

Integrates [nikic/fast-route](https://github.com/nikic/FastRoute) with `phpnomad/rest` as a runtime REST router. This is the standard non-WordPress `RestStrategy` for PHPNomad applications. It hosts controllers defined against `phpnomad/rest` inside a FastRoute dispatcher, binds PHP-superglobal-backed `Request` and `Response` implementations for `phpnomad/http`, and dispatches routes in response to a `RequestInitiated` event.

## Installation

The Composer package name is `phpnomad/fastroute-rest-integration`, even though the repository is named `fastroute-integration`.

```bash
composer require phpnomad/fastroute-rest-integration
```

## What This Provides

- `RestStrategy` implementing `PHPNomad\Rest\Interfaces\RestStrategy`, backed by `nikic/fast-route`. It registers controllers as FastRoute handlers, runs any middleware declared on the controller, and runs interceptors after the response is built.
- `Request` and `Response` classes that implement the `phpnomad/http` interfaces. `Request` reads from `$_SERVER`, `$_REQUEST`, and `php://input`. `Response` is an in-memory status, headers, and body holder with JSON and error helpers.
- `RestInitializer`, a loader initializer that registers the bindings above and attaches a `DispatchRequest` listener to the `RequestInitiated` event.

## Requirements

- [`phpnomad/rest`](https://packagist.org/packages/phpnomad/rest), the abstraction this integration implements
- [`phpnomad/loader`](https://packagist.org/packages/phpnomad/loader), to run the initializer
- [`nikic/fast-route`](https://github.com/nikic/FastRoute), pulled in automatically by Composer

## Usage

Add `RestInitializer` to the initializer list you pass to the `Bootstrapper`. Once `load()` runs, your controllers are routed through FastRoute and dispatched when a `RequestInitiated` event fires.

```php
<?php

use PHPNomad\Core\Bootstrap\CoreInitializer;
use PHPNomad\Di\Container\Container;
use PHPNomad\FastRoute\Component\RestInitializer;
use PHPNomad\Loader\Bootstrapper;

$container = new Container();

(new Bootstrapper(
    $container,
    new CoreInitializer(),
    new RestInitializer(),
    new MyAppInitializer()
))->load();
```

`MyAppInitializer` is where you register your own controllers via `HasControllers`. See the [bootstrapping guide at phpnomad.com](https://phpnomad.com) for the full picture.

## Documentation

Full PHPNomad documentation lives at [phpnomad.com](https://phpnomad.com), including the `phpnomad/rest` reference and the bootstrapping guide. Upstream router documentation lives at [nikic/fast-route](https://github.com/nikic/FastRoute).

## License

MIT. See [LICENSE](LICENSE).
