<?php

namespace Test\Lucinda\MVC\Response;

use Lucinda\MVC\Response\Redirect;
use Lucinda\UnitTest\Result;

class RedirectTest
{
    private Redirect $redirect;

    public function __construct()
    {
        $this->redirect = new Redirect("https://www.google.com");
    }

    public function setPermanent()
    {
        $this->redirect->setPermanent(true);
        return new Result(true);
    }


    public function setPreventCaching()
    {
        $this->redirect->setPreventCaching(true);
        return new Result(true);
    }


    public function run()
    {
        return new Result(false, "Redirection cannot be unit tested");
    }
}
