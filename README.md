# Console MVC API

`lucinda/console-mvc` is a small integration package for running Lucinda MVC applications from the command line and writing the resolved response to STDOUT.

It sits on top of [`lucinda/abstract_mvc`](https://github.com/aherne/abstract_mvc) and adds console-specific request detection, route/format validation, and a front controller that wires the request lifecycle together.

## Suggested Structure

For this package type, the README works better when organized around integration flow instead of a full class dump:

1. package purpose
2. installation
3. request lifecycle
4. XML configuration
5. integration example
6. main runtime contracts
7. exceptions and constraints
8. testing

## Installation

```bash
composer require lucinda/console-mvc
```

Package requirements:

- PHP `^8.1`
- `ext-simplexml`
- `lucinda/abstract_mvc` `^3.0`

## What The Package Does

The package currently exposes these runtime entry points:

- `Lucinda\ConsoleSTDOUT\FrontController`: orchestrates the console MVC flow.
- `Lucinda\ConsoleSTDOUT\Application`: thin extension of the base MVC application parser.
- `Lucinda\ConsoleSTDOUT\Request`: reads the console route, extra arguments, OS, current user, and `php://input`.
- `Lucinda\ConsoleSTDOUT\Request\Validator`: validates the route and output format against XML configuration.
- `Lucinda\ConsoleSTDOUT\RouteNotFoundException`: thrown when the requested route does not exist.

## Request Lifecycle

`FrontController::run()` performs the following steps:

1. runs `START` event listeners from the provided `EventScheduler`
2. loads the XML application configuration
3. runs `APPLICATION` listeners
4. builds a `Request` from `$_SERVER["argv"]`
5. validates route and format through `Request\Validator`
6. runs `REQUEST` listeners
7. creates a console response
8. runs the configured controller for the matched route, if one exists
9. resolves the selected view through the configured resolver, if a view is available
10. applies `RESPONSE` listeners that implement `Lucinda\MVC\Response\Transformer\Body`
11. writes the response to STDOUT
12. runs `END` listeners

If a `Lucinda\MVC\TerminationException` is thrown anywhere in the flow, its embedded response is rendered immediately.

## Configuration

The constructor expects the path to an XML entry file:

```php
new FrontController($xmlPath, $eventScheduler);
```

That entry file should reference the same three MVC sections exercised by the test fixtures:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xml>
<xml>
  <application ref="application"/>
  <resolvers ref="resolvers"/>
  <routes ref="routes"/>
</xml>
```

### Application

`application.xml` defines the defaults used during validation and view lookup. The active tests use:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xml>
<xml>
  <application
    default_format="txt"
    default_route="index"
    views_folder="tests/fixtures/views"
    views_extension="txt"
    version="1.0.0"
  />
</xml>
```

Key points enforced by the current code:

- when no route argument is provided, `default_route` is used
- the selected output format starts from `default_format`
- views are located through the underlying MVC package using `views_folder` and `views_extension`

### Resolvers

Resolvers map an output format to a view resolver class:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xml>
<xml>
  <resolvers>
    <resolver format="txt" class="App\Resolvers\TextResolver"/>
  </resolvers>
</xml>
```

The resolved class is instantiated through dependency injection and used to convert a `Lucinda\MVC\Response\View` into the response body.

### Routes

Routes map CLI route names to controllers, views, and optional per-route formats:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xml>
<xml>
  <routes>
    <route id="index" view="index"/>
    <route id="users" controller="App\Controllers\UsersController" view="users"/>
    <route id="export" controller="App\Controllers\ExportController" view="export" format="json"/>
  </routes>
</xml>
```

Current validation rules:

- the route is read from `$_SERVER["argv"][1]`
- extra CLI arguments start at `$_SERVER["argv"][2]`
- an empty route falls back to `default_route`
- an unknown route throws `RouteNotFoundException`
- an unknown format throws `Lucinda\MVC\ConfigurationException`

## Integration Example

This example matches the current constructor and event binding approach used by the repository’s runnable fixture:

```php
<?php

require __DIR__."/vendor/autoload.php";

use Lucinda\ConsoleSTDOUT\FrontController;
use Lucinda\MVC\EventScheduler;
use Lucinda\MVC\EventType;
use App\EventListeners\StartListener;
use App\EventListeners\ApplicationListener;
use App\EventListeners\RequestListener;
use App\EventListeners\EndListener;

$scheduler = new EventScheduler();
$scheduler->add(EventType::START, StartListener::class);
$scheduler->add(EventType::APPLICATION, ApplicationListener::class);
$scheduler->add(EventType::REQUEST, RequestListener::class);
$scheduler->add(EventType::END, EndListener::class);

$frontController = new FrontController(__DIR__."/console.xml", $scheduler);
$frontController->run();
```

Run it from the shell like this:

```bash
php index.php users 42
```

In that command:

- `users` becomes the route
- `42` is available as the first request parameter

## Main Contracts

### `Request`

`Lucinda\ConsoleSTDOUT\Request` is immutable after construction and exposes:

- `getRoute(): string`
- `parameters(int $index = -1): array|string|null`
- `getOperatingSystem(): string`
- `getUserInfo(): Lucinda\ConsoleSTDOUT\Request\UserInfo`
- `getInputStream(): string`

`parameters()` returns all CLI parameters when called without an index, or a single parameter by position when an index is provided.

### `Request\UserInfo`

`Lucinda\ConsoleSTDOUT\Request\UserInfo` exposes:

- `getName(): string`
- `isSuper(): bool`

User detection is OS-sensitive:

- on POSIX systems, the username prefers `posix_getpwuid(posix_geteuid())`
- otherwise it falls back to `$_SERVER["USER"]` and then `get_current_user()`
- superuser detection is `root` on non-Windows systems
- on Windows, admin detection relies on `shell_exec("net session")`

### Controllers And Resolvers

The front controller works with contracts from `lucinda/abstract_mvc`:

- route controllers are instantiated from the XML `controller` class
- if a controller implements `Lucinda\MVC\Controller\ViewAware`, its `run()` return value is used as the filled view
- resolvers are instantiated from the XML `resolver` class
- response transformers are taken from `RESPONSE` events only when the listener implements `Lucinda\MVC\Response\Transformer\Body`

## Exceptions And Constraints

- `Request` throws `Lucinda\MVC\ConfigurationException` if the process was not started from a CLI context with `$_SERVER["argv"]`
- `Request\Validator` throws `RouteNotFoundException` when the route is missing from `<routes>`
- `Request\Validator` throws `Lucinda\MVC\ConfigurationException` when the resolved format is missing from `<resolvers>`
- `FrontController` catches `Lucinda\MVC\TerminationException` and renders its response directly

Practical constraints from the current implementation:

- the route always comes from the first CLI argument
- parameter parsing is positional only
- this package does not define its own event listener base classes; scheduling relies on `Lucinda\MVC\EventScheduler`
- `RESPONSE` listeners are not executed generically; only body transformers affect the response in this package

## Testing

Run the repository tests with:

```bash
php test.php
```

The active test suite covers:

- request parsing
- route and format validation
- route-not-found behavior
- front controller lifecycle and event ordering

