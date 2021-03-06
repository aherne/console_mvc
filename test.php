<?php

require __DIR__ . '/vendor/autoload.php';

try {
    define("ENVIRONMENT", (getenv("ENVIRONMENT") ? getenv("ENVIRONMENT") : "local"));
    new Lucinda\UnitTest\ConsoleController("unit-tests.xml", ENVIRONMENT);
} catch (Exception $e) {
    var_dump($e);
    echo $e->getMessage();
}
