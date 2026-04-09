<?php
namespace Test\Lucinda\ConsoleSTDOUT\Request;

use Lucinda\ConsoleSTDOUT\Request\UserInfo;
use Lucinda\UnitTest\Result;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;

class UserInfoTest
{
    private function getExpectedUserName(array $server): string
    {
        if (function_exists("posix_getpwuid")) {
            return posix_getpwuid(posix_geteuid())["name"];
        }
        if (!empty($server["USER"])) {
            return $server["USER"];
        }

        return get_current_user();
    }

    public function getName(): Result
    {
        $server = ["USER" => "tester"];
        $userInfo = new UserInfo("Linux", $server);

        return (new Strings($userInfo->getName()))->assertEquals($this->getExpectedUserName($server));
    }

    public function isSuper(): Result
    {
        $userInfo = new UserInfo("Linux", ["USER" => "tester"]);
        $expected = ($userInfo->getName() == "root");

        return $expected
            ? (new Booleans($userInfo->isSuper()))->assertTrue()
            : (new Booleans($userInfo->isSuper()))->assertFalse();
    }
}
