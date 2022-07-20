<?php
namespace Lucinda\ConsoleSTDOUT\Request;

/**
 * Detects information about user API is ran by
 */
class UserInfo
{
    private string $name;
    private bool $isSuper = false;

    /**
     * Detects info by operating system name
     *
     * @param string $operatingSystem
     */
    public function __construct(string $operatingSystem)
    {
        $this->setName();
        $this->setIsSuper($operatingSystem);
    }

    /**
     * Detects running user name
     */
    private function setName(): void
    {
        if (function_exists("posix_getpwuid")) {
            $this->name = posix_getpwuid(posix_geteuid())["name"];
        } elseif (!empty($_SERVER["USER"])) {
            $this->name = $_SERVER["USER"];
        } else {
            $this->name = get_current_user();
        }
    }

    /**
     * Gets name of user that runs API
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Checks if running user is root/superuser by operating system
     *
     * @param string $operatingSystem
     */
    private function setIsSuper(string $operatingSystem): void
    {
        if (stripos($operatingSystem, "win")===0) {
            $result = shell_exec("net session");
            if (!str_contains($result, "Access is denied.")) {
                $this->isSuper = true;
            }
        } else {
            $this->isSuper = ($this->name == "root");
        }
    }

    /**
     * Gets if running user is root/superuser
     *
     * @return bool
     */
    public function isSuper(): bool
    {
        return $this->isSuper;
    }
}
