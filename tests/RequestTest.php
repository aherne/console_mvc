<?php

namespace Test\Lucinda\ConsoleSTDOUT;

use Lucinda\ConsoleSTDOUT\Request;
use Lucinda\UnitTest\Result;

class RequestTest
{
    private $object;

    public function __construct()
    {
        $_SERVER["argv"] = ["index.php", "test", "me"];
        $this->object = new Request();
    }

    public function getRoute()
    {
        return new Result($this->object->getRoute()=="test");
    }


    public function parameters()
    {
        return new Result($this->object->parameters()==["me"]);
    }


    public function getOperatingSystem()
    {
        return new Result($this->object->getOperatingSystem()==php_uname("s"));
    }


    public function getUserInfo()
    {
        $userName = "";
        if (function_exists("posix_getpwuid")) {
            $userName = posix_getpwuid(posix_geteuid())["name"];
        } elseif (!empty($_SERVER["USER"])) {
            $userName = $_SERVER["USER"];
        } else {
            $userName = get_current_user();
        }
        return new Result($this->object->getUserInfo()->getName()==$userName);
    }


    public function getInputStream()
    {
        return new Result($this->object->getInputStream()==="");
    }
}
