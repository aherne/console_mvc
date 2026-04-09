<?php

namespace Test\Lucinda\ConsoleSTDOUT\Support\Resolvers;

use Lucinda\MVC\Response\View;
use Lucinda\MVC\Response\ViewResolver;

class JsonResolver implements ViewResolver
{
    public function resolve(View $view): string
    {
        return json_encode($view->getData()) ?: "";
    }
}
