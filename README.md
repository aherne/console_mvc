# Console MVC API

Table of contents:

- [About](#about)
- [Configuration](#configuration)
- [Binding Points](#binding-points)
- [Execution](#execution)
    - [Initialization](#initialization)
    - [Binding Events](#binding-events)
    - [Configuring Shared Variables](#configuring-shared-variables)
    - [Handling](#handling)
- [Installation](#installation)
- [Running Requests](#running-requests)
- [Unit Tests](#unit-tests)
- [Reference Guide](#reference-guide)
- [Specifications](#specifications)
    - [How Is Response Format Detected](#how-is-response-format-detected)
    - [How Are View Resolvers Located](#how-are-view-resolvers-located)
    - [How Is Route Detected](#how-is-route-detected)
    - [How Are Controllers Located](#how-are-controllers-located)
    - [How Are Parameter Validators Working](#how-are-parameter-validators-working)
    - [How to Set Cookies Path and Domain](#how-to-set-cookies-path-and-domain)
    - [How Are Uploaded Files Processed](#how-are-uploaded-files-processed)
    - [How Is Requested URI Processed](#how-is-requested-uri-processed)
    - [How Are Views Located](#how-are-views-located)

## About

This API is a **skeleton** (requires [binding](#binding-points) by developers) created to efficiently handle console requests into server responses using a MVC version where views and models are expected to be independent while controllers mediate between the two based on user request. Designed with modularity, efficiency and simplicity at its foundation, API is both object and event oriented: similar to JavaScript, it allows developers to bind logic that will be executed when predefined events are reached while handling.

![diagram](https://www.lucinda-framework.com/console-mvc-api.svg)

API does nothing more than standard MVC logic, so it may need a framework to be built on top to add further features (eg: DB connectivity). In order to use it, following steps are required from developers:

- **[configuration](#configuration)**: setting up an XML file where this API is configured
- **[binding points](#binding-points)**: binding user-defined components defined in XML/code to API prototypes in order to gain necessary abilities
- **[initialization](#initialization)**: instancing [FrontController](https://github.com/aherne/console-mvc/blob/master/src/FrontController.php), a [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php) able to handle requests into responses later on based on above two
- **[binding events](#binding-events)**: setting up [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php) classes that will be instanced and *run* when predefined events are reached during handling process
- **[configuring shared variables](#configuring-shared-variables)**: extend [Attributes](#class-attributes) class to encapsulate variables specific to your project, to be shared between event listeners and controllers
- **[handling](#handling)**: calling *run* method @ [FrontController](https://github.com/aherne/console-mvc/blob/master/src/FrontController.php)  to finally handle requests into responses, triggering events above (if any)

API is fully PSR-4 compliant, only requiring [Abstract MVC API](https://github.com/aherne/mvc) for basic MVC logic, PHP7.1+ interpreter and SimpleXML extension. To quickly see how it works, check:

- **[installation](#installation)**: describes how to install API on your computer, in light of steps above
- **[running requests](#running-requests)**: describes how to use installed and configured API to run console requests
- **[reference guide](#reference-guide)**: describes all API classes, methods and fields relevant to developers
- **[unit tests](#unit-tests)**: API has 100% Unit Test coverage, using [UnitTest API](https://github.com/aherne/unit-testing) instead of PHPUnit for greater flexibility
- **[example](https://github.com/aherne/console-mvc/blob/master/tests/FrontController.php)**: shows a deep example of API functionality based on [FrontController](https://github.com/aherne/console-mvc/blob/master/src/FrontController.php) unit test

All classes inside belong to **Lucinda\ConsoleSTDOUT** namespace!

## Configuration

To configure this API you must have a XML with following tags inside:

- **[application](#application)**: (mandatory) configures your application on a general basis
- **[resolvers](#resolvers)**: (mandatory) configures formats in which your application is able to resolve responses to
- **[routes](#routes)**: (mandatory) configures routes that bind requested resources to controllers and views

### Application

Tag documentation is completely covered by inherited Abstract MVC API [specification](https://github.com/aherne/mvc#application)! Since STDIN for this API is made of HTTP(s) requests, value of *default_route* attribute must point to **index** (homepage) for requests that come with no route. 

### Resolvers

Tag documentation is completely covered by inherited Abstract MVC API [specification](https://github.com/aherne/mvc#resolvers)!

### Routes

Maximal syntax of this tag is:

```xml
<routes>
    <route id="..." controller="..." view="..." format="..."/>
    ...
</routes>
```

Most of tag logic is already covered by Abstract MVC API [specification](https://github.com/aherne/mvc#routes). Following extra observations need to be made:

- *id*: (mandatory) requested route identified by *first console argument* when running API!
- *controller*: (optional) name of user-defined PS-4 autoload compliant class (including namespace) that will mitigate requests and responses based on models.<br/>Class must be a [Controller](#abstract-class-controller) instance!

Tag example:

```xml
<routes>
    <route id="users" controller="Lucinda\Project\Controllers\UsersSynchronization" view="users"/>
    <route id="groups" controller="Lucinda\Project\Controllers\GroupsSynchronization" view="groups">
</routes>
```

**^ It is mandatory to define a route for that defined by default_route attribute @ [application](#application) XML tag!**

If request came without route, **default** route is used. If, however, request came with a route that matches no **id**, a [RouteNotFoundException](https://github.com/aherne/console-mvc/blob/master/src/RouteNotFoundException.php) is thrown!

## Binding Points

In order to remain flexible and achieve highest performance, API takes no more assumptions than those absolutely required! It offers developers instead an ability to bind to its prototypes in order to gain certain functionality.

### Declarative Binding

It offers developers an ability to **bind declaratively** to its prototype classes/interfaces via XML:

| XML Attribute @ Tag | Class Prototype | Ability Gained |
| --- | --- | --- |
| [controller @ route](#routes) | [Controller](#abstract-class-controller) | MVC controller for any request URI |
| [class @ resolver](#resolvers) | [\Lucinda\MVC\ViewResolver](https://github.com/aherne/mvc#Abstract-Class-ViewResolver) | Resolving response in a particular format (eg: html) |

### Programmatic Binding

It offers developers an ability to **bind programmatically** to its prototypes via [FrontController](#initialization) constructor:

| Class Prototype | Ability Gained |
| --- | --- |
| [Attributes](#class-attributes) | (mandatory) Collects data (via setters and getters) to be made available throughout request-response cycle |

and addEventListener method (see: [Binding Events](#binding-events) section)!

## Execution

### Initialization

Now that developers have finished setting up XML that configures the API, they are finally able to initialize it by instantiating [FrontController](https://github.com/aherne/console-mvc/blob/master/src/FrontController.php).

Apart of method *run* required by [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php) interface it implements, class comes with following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $documentDescriptor, [Attributes](#class-attributes) $attributes | void | Records user defined XML and attributes for later handling |
| addEventListener | string $type, string $className | void | Binds a listener to an event type |

Where:

- *$documentDescriptor*: relative location of XML [configuration](#configuration) file. Example: "configuration.xml"
- *$attributes*: see **[configuring shared variables](#configuring-shared-variables)**.
- *$type*: event type (see above) encapsulated by enum [EventType](https://github.com/aherne/console-mvc/blob/master/src/EventType.php)
- *$className*: listener *class name*, including namespace and subfolder, found in *folder* defined when [Attributes](#class-attributes) was instanced.

Example:

```php
$handler = new FrontController("configuration.xml", new MyCustomAttributes("application/event_listeners");
$handler->run();
```

### Binding Events

As mentioned above, API allows developers to bind listeners to handling lifecycle events via *addEventListener* method above. Each event  type corresponds to a abstract [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php) class:

| Type | Class | Description |
| --- | --- | --- |
| START | [EventListeners\Start](#abstract-class-eventlisteners-start) | Ran before [configuration](#configuration) XML is read |
| APPLICATION | [EventListeners\Application](#abstract-class-eventlisteners-application) | Ran after [configuration](#configuration) XML is read into [Lucinda\MVC\Application](https://github.com/aherne/mvc#class-application) |
| REQUEST | [EventListeners\Request](#abstract-class-eventlisteners-request) | Ran after user request is read into [Request](#class-request) object |
| RESPONSE | [EventListeners\Response](#abstract-class-eventlisteners-response) | Ran after [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) body is compiled but before it's rendered |
| END | [EventListeners\End](#abstract-class-eventlisteners-end) | Ran after [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) was rendered back to caller  |

Listeners must extend matching event class and implement required *run* method holding the logic that will execute when event is triggered. It is required for them to be registered BEFORE *run* method is ran:

```php
$handler = new FrontController("stdout.xml", new FrameworkAttributes();
$handler->addEventListener(EventType::APPLICATION, Lucinda\Project\EventListeners\Logging::class);
$handler->run();
```

To understand how event listeners are located, check [specifications](#how-are-event-listeners-located)!

### Configuring Shared Variables

API allows event listeners to set variables that are going to be made available to subsequent event listeners and controllers. For each variable there is a:

- *setter*: to be ran once by a event listener
- *getter*: to be ran by subsequent event listeners and controllers

API comes with [Attributes](#class-attributes), which holds the foundation every site must extend in order to set up its own variables. Unless your site is extremely simple, it will require developers to extend this class and add further variables, for whom setters and getters must be defined!

### Handling

Once above steps are done, developers are finally able to handle requests into responses via *run* method of [FrontController](https://github.com/aherne/console-mvc/blob/master/src/FrontController.php), which:

- detects [EventListeners\Start](#abstract-class-eventlisteners-start) listeners and executes them in order they were registered
- encapsulates [configuration](#configuration) XML file into [Lucinda\MVC\Application](https://github.com/aherne/mvc#class-application) object
- detects [EventListeners\Application](#abstract-class-eventlisteners-application) listeners and executes them in order they were registered
- encapsulates request information (environment info, machine info, request info) into [Request](#class-request) object
- detects [EventListeners\Request](#abstract-class-eventlisteners-request) listeners and executes them in order they were registered
- initializes empty [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) based on information detected above from request or XML
- locates [Controller](#abstract-class-controller) based on information already detected and, if found, executes it in order to bind models to views
- locates [Lucinda\MVC\ViewResolver](https://github.com/aherne/mvc#abstract-class-viewresolver) based on information already detected and executes it in order to feed response body based on view
- detects [EventListeners\Response](#abstract-class-eventlisteners-response) listeners and executes them in order they were registered
- sends [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) back to caller, containing headers and body
- detects [EventListeners\End](#abstract-class-eventlisteners-end) listeners and executes them in order they were registered

All components that are in developers' responsibility ([Controller](#abstract-class-controller), [Lucinda\MVC\ViewResolver](https://github.com/aherne/mvc#abstract-class-viewresolver), along with event listeners themselves, implement [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php) interface.

## Installation

First choose a folder, then write this command there using console:

```console
composer require lucinda/console-mvc
```

Rename folder above to DESTINATION_FOLDER then create a *configuration.xml* file holding configuration settings (see [configuration](#configuration) above) and a *index.php* file (see [initialization](#initialization) above) in project root with following code:

```php
$controller = new Lucinda\ConsoleSTDOUT\FrontController("configuration.xml", new Attributes("application/events"));
// TODO: add event listeners here
$controller->run();
```

## Running Requests

Now that you have installed project on your machine, go to DESTINATION_FOLDER, open console/terminal and write:

```console
php index.php ROUTE PARAM1 PARAM2 ...
```

Where:

- ROUTE: route to be handled (must be matched with a **[route](#routes)** XML subtag)
- PARAM1, ...: parameters to send to route, accessible in controllers/listeners as: *$this->request->parameters*

## Unit Tests

For tests and examples, check following files/folders in API sources:

- [test.php](https://github.com/aherne/console-mvc/blob/master/test.php): runs unit tests in console
- [unit-tests.xml](https://github.com/aherne/console-mvc/blob/master/unit-tests.xml): sets up unit tests and mocks "loggers" tag
- [tests](https://github.com/aherne/console-mvc/blob/master/tests): unit tests for classes from [src](https://github.com/aherne/console-mvc/blob/master/src) folder

## Reference Guide

These classes are fully implemented by API:

- [Request](#class-request): encapsulates request information (route, parameters, user info, etc.)
    - [Request\UserInfo](#class-request-userinfo): encapsulates information about console user that made request

Apart of classes mentioned in **[binding events](#binding-events)**, following abstract classes require to be extended by developers in order to gain an ability:

- [Controller](#abstract-class-controller): encapsulates binding [Request](#class-request) to [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) based on user request and XML info

### Class Request

Class [Request](https://github.com/aherne/console-mvc/blob/master/src/Request.php) encapsulates information detected about user request based on superglobals ($\_SERVER, $\_GET, $\_POST, $\_FILES) and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getRoute | void | string | Gets first console argument received by API. See [execution](#execution) above! |
| getInputStream | void | string | Gets access to input stream for binary requests. |
| parameters | void | array | Gets all console arguments received by API, minus first (route). See [execution](#execution) above! |
| parameters | int $position | mixed | Gets value of console arguments by position. |
| getOperatingSystem | void | string | Gets operating system name API is running into. |
| getUserInfo | void | [Request\UserInfo](#class-request-userinfo) | Gets information about *user* that made request. |

### Class Request UserInfo

Class [Request\UserInfo](https://github.com/aherne/console-mvc/blob/master/src/Request/UserInfo.php) encapsulates information detected about user that made request and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getName | void | string | Gets requester user name |
| isSuper | void | bool | Gets whether or not requester is a superuser/root |

### Abstract Class EventListeners Start

Abstract class [EventListeners\Start](https://github.com/aherne/console-mvc/blob/master/src/EventListeners/Start.php) implements [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php)) and listens to events that execute BEFORE [configuration](#configuration) XML is read.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

A common example of a START listener is the need to set start time, in order to benchmark duration of handling later on:

```php
class StartBenchmark extends Lucinda\ConsoleSTDOUT\EventListeners\Start
{
    public function run(): void
    {
        // you will first need to extend Application and add: setStartTime, getStartTime
        $this->attributes->setStartTime(microtime(true));
    }
}
```

### Abstract Class EventListeners Application

Abstract class [EventListeners\Application](https://github.com/aherne/console-mvc/blob/master/src/EventListeners/Application.php) implements [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php)) and listens to events that execute AFTER [configuration](#configuration) XML is read.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Lucinda\MVC\Application](https://github.com/aherne/mvc#class-application) | Gets application information detected from XML. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

TODO: usage example

### Abstract Class EventListeners Request

Abstract class [EventListeners\Request](https://github.com/aherne/console-mvc/blob/master/src/EventListeners/Request.php) implements [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php)) and listens to events that execute AFTER [Request](#class-request) object is created.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Lucinda\MVC\Application](https://github.com/aherne/mvc#class-application) | Gets application information detected from XML. |
| $request | [Request](#class-request) | Gets request information. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

TODO: usage example

### Abstract Class EventListeners Response

Abstract class [EventListeners\Response](https://github.com/aherne/console-mvc/blob/master/src/EventListeners/Response.php) implements [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php)) and listens to events that execute AFTER [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) body was set but before it's committed back to caller.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Lucinda\MVC\Application](https://github.com/aherne/mvc#class-application) | Gets application information detected from XML. |
| $request | [Request](#class-request) | Gets request information. |
| $response | [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) | Gets access to object based on which response can be manipulated. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

TODO: usage example

### Abstract Class EventListeners End

Abstract class [EventListeners\End](https://github.com/aherne/console-mvc/blob/master/src/EventListeners/End.php) implements [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php)) and listens to events that execute AFTER [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) was rendered back to caller.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Lucinda\MVC\Application](https://github.com/aherne/mvc#class-application) | Gets application information detected from XML. |
| $request | [Request](#class-request) | Gets request information. |
| $response | [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) | Gets access to object based on which response can be manipulated. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

A common example of a START listener is the need to set end time, in order to benchmark duration of handling:

```php
class EndBenchmark extends Lucinda\ConsoleSTDOUT\EventListeners\End
{
    public function run(): void
    {
        $benchmark = new Benchmark();
        $benchmark->save($this->attributes->getStartTime(), microtime(true));
    }
}
```

### Abstract Class Controller

Abstract class [Controller](https://github.com/aherne/console-mvc/blob/master/src/Controller.php) implements [Runnable](https://github.com/aherne/console-mvc/blob/master/src/Runnable.php)) to set up response (views in particular) by binding information detected beforehand to models. It defines following public method relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| run | void | void | Inherited prototype to be implemented by developers to set up response based on information saved by constructor |

Developers need to implement *run* method for each controller, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Lucinda\MVC\Application](https://github.com/aherne/mvc#class-application) | Gets application information detected from XML. |
| $request | [Request](#class-request) | Gets request information. |
| $response | [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) | Gets access to object based on which response can be manipulated. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data set by event listeners beforehand. |

TODO: usage example

To understand more about how controllers are detected, check [specifications](#how-are-controllers-located)!

### Class Attributes

Class [Attributes](https://github.com/aherne/console-mvc/blob/master/src/Attributes.php) encapsulates data collected throughout request-response cycle, each corresponding to a getter and a setter, and made available to subsequent event listeners or controllers. API already comes with following:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getValidFormat | void | string | Gets final response format to use |
| getValidRoute | void | string | Gets final route requested |

Most of data collected will need to be set by developers themselves to fit their project demands so in 99% of cases class will need to be extended for each project!

TODO: usage example

## Specifications

Since this API works on top of [Abstract MVC API](https://github.com/aherne/mvc) specifications it follows their requirements and adds extra ones as well:

- [How Is Response Format Detected](#how-is-response-format-detected)
- [How Are View Resolvers Located](#how-are-view-resolvers-located)
- [How Is Route Detected](#how-is-route-detected)
- [How Are Controllers Located](#how-are-controllers-located)
- [How Are Request Parameters Detected](#how-are-request-parameters-detected)
- [How Are Views Located](#how-are-views-located)

### How Is Response Format Detected

This follows parent API [specifications](https://github.com/aherne/mvc#how-is-response-format-detected) only that routes are detected based on value of *$_SERVER["REQUEST_URI"]*.

### How Are View Resolvers Located

This follows parent API [specifications](https://github.com/aherne/mvc#how-are-view-resolvers-located) in its entirety.

### How Is Route Detected

This follows parent API [specifications](https://github.com/aherne/mvc#how-are-view-resolvers-located) only that routes are detected based on first argument received by API in console request. 

```console
php index.php ROUTE PARAM1 PARAM2 ...
```

Let's take this XML for example:

```xml
<application default_route="index" ...>
	...
</application>
<routes>
    <route id="index" .../>
    <route id="users" .../>
</routes>
```

There will be following situations for above:

| If Route Requested | Then Route ID Detected | Description |
| --- | --- | --- |
|  | index | Because requested route came empty, that identified by *default_route* is used |
| users | users | Because requested route is matched to a route, specific route is used |
| hello | - | Because no route is found matching the one requested a [RouteNotFoundException](https://github.com/aherne/console-mvc/blob/master/src/RouteNotFoundException.php) is thrown |

### How Are Controllers Located

This follows parent API [specifications](https://github.com/aherne/mvc#how-are-controllers-located) only that class defined as *controller* attribute in [route](#routes) tag must extend [Controller](#abstract-class-controller).

### How Are Request Parameters Detected

Users are able to send one or more request parameters in API request:

```console
php index.php ROUTE PARAM1 PARAM2 ...
```

Then query in controllers/event-listeners those parameters via:

```php
$parameters = $this->request->parameters();
```

If original request was:

```console
php index.php users hello world
```

Then route will be "users" and parameters will be ["hello", "world"]!

### How Are Views Located

This follows parent API [specifications](https://github.com/aherne/mvc#how-are-views-located) in its entirety. Extension is yet to be decided, since it depends on type of view resolved!