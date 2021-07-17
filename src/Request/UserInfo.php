<?php
namespace Lucinda\ConsoleSTDOUT\Request;

/**
 * Detects information about user API is ran by
 */
class UserInfo
{
    private $name;
    private $isSuper = false;
    
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
            $processUser = posix_getpwuid(posix_geteuid());
            $this->name = $processUser["name"];
        } else if (!empty($_SESSION["USER"])) {
            $this->name = $_SESSION["USER"];
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
        if (stripos($operatingSystem, "win")!==false) {
            $result = shell_exec("net session");
            if (strpos($result, "Access is denied.")===false) {
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
