<?php

namespace Lucinda\MVC\Response;

use Lucinda\MVC\Response;

/**
 * Redirects to a new location
 */
class Redirect implements Response
{
    const SUPPORTED_STATUSES = [301, 302, 303, 304, 307, 308];
    private HttpStatus $status;
    private string $location;
    private bool $preventCaching = false;

    /**
     * Sets URL to redirect to
     *
     * @param string $location
     * @param HttpStatus $status
     */
    public function __construct(string $location, HttpStatus $status = HttpStatus::FOUND)
    {
        $this->location = $location;
        $this->status = $status;
        if (!in_array($this->status->value, self::SUPPORTED_STATUSES)) {
            throw new Exception("Invalid redirection status: ".$status->value);
        }
    }

    /**
     * Sets whether browsers should prevent caching redirection
     *
     * @param  bool $flag
     * @return void
     */
    public function setPreventCaching(bool $flag): void
    {
        $this->preventCaching = $flag;
    }

    /**
     * Executes redirection logic
     *
     * @return void
     */
    public function run(): void
    {
        if ($this->preventCaching) {
            header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
            header("Pragma: no-cache");
            header("Expires: 0");
        }
        header('Location: '.$this->location, true, $this->status->value);
        exit();
    }
}