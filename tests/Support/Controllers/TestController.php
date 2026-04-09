<?php

namespace Test\Lucinda\ConsoleSTDOUT\Support\Controllers;

use Lucinda\ConsoleSTDOUT\Request;
use Lucinda\MVC\Controller\ViewAware;
use Lucinda\MVC\Response\View;

class TestController implements ViewAware
{
    public function __construct(private Request $request)
    {
    }

    public function run(): View
    {
        return new View([
            "argument" => (string) $this->request->parameters(0)
        ]);
    }
}
