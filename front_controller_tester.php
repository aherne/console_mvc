<?php

require "vendor/autoload.php";

use Lucinda\ConsoleSTDOUT\FrontController;
use Lucinda\MVC\EventScheduler;
use Lucinda\MVC\EventType;
use Test\Lucinda\ConsoleSTDOUT\Support\EventListeners\ApplicationListener;
use Test\Lucinda\ConsoleSTDOUT\Support\EventListeners\EndListener;
use Test\Lucinda\ConsoleSTDOUT\Support\EventListeners\RequestListener;
use Test\Lucinda\ConsoleSTDOUT\Support\EventListeners\StartListener;

$_SERVER["argv"] = [
    __FILE__,
    $argv[2] ?? "",
    $argv[3] ?? ""
];

$scheduler = new EventScheduler();
$scheduler->add(EventType::START, StartListener::class);
$scheduler->add(EventType::APPLICATION, ApplicationListener::class);
$scheduler->add(EventType::REQUEST, RequestListener::class);
$scheduler->add(EventType::END, EndListener::class);

$frontController = new FrontController($argv[1], $scheduler);
$frontController->run();
