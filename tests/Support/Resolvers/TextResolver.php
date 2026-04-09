<?php

namespace Test\Lucinda\ConsoleSTDOUT\Support\Resolvers;

use Lucinda\MVC\Response\View;
use Lucinda\MVC\Response\ViewResolver;

class TextResolver implements ViewResolver
{
    public function resolve(View $view): string
    {
        $data = $view->getData();
        $contents = (string) file_get_contents($view->getFile());
        foreach ($data as $key => $value) {
            $contents = str_replace("{{".$key."}}", (string) $value, $contents);
        }
        return $contents;
    }
}
