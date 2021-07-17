<?php
namespace Test\Lucinda\ConsoleSTDOUT\Request;

use Lucinda\ConsoleSTDOUT\Request\UserInfo;
use Lucinda\UnitTest\Result;

class UserInfoTest
{
    private $object;

    public function __construct()
    {
        $this->object = new UserInfo(PHP_OS);
    }

    public function getName()
    {
        if (function_exists("posix_getpwuid")) {
            return new Result($this->object->getName()==posix_getpwuid(posix_geteuid())["name"]);
        } elseif (!empty($_SERVER["USER"])) {
            return new Result($this->object->getName()==$_SERVER["USER"]);
        } else {
            return new Result($this->object->getName()==get_current_user());
        }
    }

    public function isSuper()
    {
        return new Result($this->object->isSuper()==false);
    }
}
