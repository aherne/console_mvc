<?php

namespace Test\Lucinda\ConsoleSTDOUT\mocks\ViewResolvers;

use Lucinda\MVC\ViewResolver;

class Text extends ViewResolver
{
    public function run(): void
    {
        $view = $this->response->view();
        if ($view->getFile()) {
            if (!file_exists($view->getFile().".txt")) {
                throw new \Exception("View file not found");
            }

            ob_start();
            $_VIEW = $view->getData();
            require($view->getFile().".txt");
            $output = ob_get_contents();
            ob_end_clean();

            $this->response->setBody($output);
        }
    }
}
