<?php
namespace Test\Lucinda\ConsoleSTDOUT\mocks\Controllers;

use Lucinda\ConsoleSTDOUT\Controller;

class Test extends Controller
{
    public function run(): void
    {
        $this->response->view()["test"] = $this->request->parameters(0);
    }
}
