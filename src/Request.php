<?php

namespace Lucinda\ConsoleSTDOUT;

use Lucinda\MVC\ConfigurationException;
use Lucinda\ConsoleSTDOUT\Request\UserInfo;

/**
 * Detects information about request from $_SERVER (mainly). Once detected, parameters are immutable.
 */
class Request
{
    private string $route;
    /**
     * @var string[]
     */
    private array $parameters = array();
    private string $operatingSystem;
    private UserInfo $userInfo;

    /**
     * Detects all aspects of a request.
     *
     * @throws ConfigurationException
     */
    public function __construct()
    {
        $server = $_SERVER;
        if (!isset($server["argv"])) {
            throw new ConfigurationException("API requires being called from console!");
        }

        $this->setRoute($server);
        $this->setParameters($server);
        $this->setOperatingSystem();
        $this->setUserInfo($server);
    }

    /**
     * Sets route requested from console
     *
     * @param array<string,mixed> $server
     */
    private function setRoute(array $server): void
    {
        $this->route = ($server["argv"][1] ?? "");
    }

    /**
     * Gets route requested from console
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Sets parameters sent by client from console
     *
     * @param array<string,mixed> $server
     */
    private function setParameters(array $server): void
    {
        foreach ($server["argv"] as $i=>$value) {
            if ($i>1) {
                $this->parameters[] = $value;
            }
        }
    }

    /**
     * Gets request parameters detected by optional position
     *
     * @param  integer $index
     * @return string[]|string|null
     */
    public function parameters(int $index=-1)
    {
        if ($index == -1) {
            return $this->parameters;
        } else {
            return ($this->parameters[$index] ?? null);
        }
    }

    /**
     * Sets operating system API is running into
     */
    private function setOperatingSystem(): void
    {
        $this->operatingSystem = php_uname("s");
    }

    /**
     * Gets operating system name API is running into
     *
     * @return string
     */
    public function getOperatingSystem(): string
    {
        return $this->operatingSystem;
    }

    /**
     * Sets info about user running API from terminal/shell
     *
     * @param array<string,mixed> $server
     */
    private function setUserInfo(array $server): void
    {
        $this->userInfo = new UserInfo($this->operatingSystem, $server);
    }

    /**
     * Gets info about user running API from terminal/shell
     *
     * @return UserInfo
     */
    public function getUserInfo(): UserInfo
    {
        return $this->userInfo;
    }

    /**
     * Gets input stream contents.
     *
     * @return string
     */
    public function getInputStream(): string
    {
        return file_get_contents("php://input");
    }
}
