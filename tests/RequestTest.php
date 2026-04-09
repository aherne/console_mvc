<?php
namespace Test\Lucinda\ConsoleSTDOUT;

use Lucinda\ConsoleSTDOUT\Request;
use Lucinda\ConsoleSTDOUT\Request\UserInfo;
use Lucinda\UnitTest\Result;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;

class RequestTest
{
    private function createRequest(array $argv): Request
    {
        $_SERVER["argv"] = $argv;
        return new Request();
    }

    private function getExpectedUserName(array $server = []): string
    {
        if (function_exists("posix_getpwuid")) {
            return posix_getpwuid(posix_geteuid())["name"];
        }
        if (!empty($server["USER"])) {
            return $server["USER"];
        }

        return get_current_user();
    }

    public function getRoute(): Result
    {
        $request = $this->createRequest(["index.php", "test", "alpha", "beta"]);

        return (new Strings($request->getRoute()))->assertEquals("test");
    }

    public function parameters(): array
    {
        $request = $this->createRequest(["index.php", "test", "alpha", "beta"]);

        return [
            (new Arrays($request->parameters()))->assertEquals(["alpha", "beta"]),
            (new Strings((string) $request->parameters(0)))->assertEquals("alpha"),
            (new Strings((string) $request->parameters(1)))->assertEquals("beta"),
            (new Booleans(is_null($request->parameters(2))))->assertTrue()
        ];
    }

    public function getOperatingSystem(): Result
    {
        $request = $this->createRequest(["index.php", "test"]);

        return (new Strings($request->getOperatingSystem()))->assertEquals(php_uname("s"));
    }

    public function getUserInfo(): array
    {
        $_SERVER["argv"] = ["index.php", "test"];
        $request = new Request();

        return [
            (new Objects($request->getUserInfo()))->assertInstanceOf(UserInfo::class),
            (new Strings($request->getUserInfo()->getName()))->assertEquals($this->getExpectedUserName($_SERVER))
        ];
    }

    public function getInputStream(): Result
    {
        $request = $this->createRequest(["index.php", "test"]);

        return (new Strings($request->getInputStream()))->assertEquals("");
    }
}
