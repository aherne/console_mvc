<?php
namespace Lucinda\ConsoleSTDOUT;

use Lucinda\MVC\ConfigurationException;
use Lucinda\ConsoleSTDOUT\Request\UserInfo;

/**
 * Detects information about request from $_SERVER, $_GET, $_POST, $_FILES. Once detected, parameters are immutable.
 */
class Request
{
    private $route;
    private $parameters = array();
    private $operatingSystem;
    private $userInfo;

    /**
     * Detects all aspects of a request.
     *
     * @throws ConfigurationException
     */
    public function __construct()
    {
        if (!isset($_SERVER["argv"])) {
            throw new ConfigurationException("API requires being called from console!");
        }
        
        $this->setRoute();
        $this->setParameters();
        $this->setOperatingSystem();
        $this->setUserInfo();
    }
    
    /**
     * Sets route requested from console
     */
    private function setRoute(): void
    {
        $this->route = (isset($_SERVER["argv"][1])?$_SERVER["argv"][1]:"");
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
     */
    private function setParameters(): void
    {
        foreach ($_SERVER["argv"] as $i=>$value) {
            if ($i>1) {
                $this->parameters[] = $value;
            }
        }
    }

    /**
     * Gets request parameters detected by optional position
     *
     * @param integer $name
     * @return string[]|string|null
     */
    public function parameters(int $index=-1)
    {
        if ($index == -1) {
            return $this->parameters;
        } else {
            return (isset($this->parameters[$index])?$this->parameters[$index]:null);
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
     */
    private function setUserInfo(): void
    {
        $this->userInfo = new UserInfo($this->operatingSystem);
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
