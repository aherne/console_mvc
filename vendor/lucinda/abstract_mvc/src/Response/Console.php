<?php

namespace Lucinda\MVC\Response;

/**
 * Implements a response expected to be displayed in a console / terminal
 */
abstract class Console extends Basic
{
    private ?int $exitCode = null;
    private ?string $errorBody = null;

    /**
     * Sets exit code
     * 
     * @param int $exitCode
     */
    public function setExitCode(int $exitCode): void
    {
        if ($exitCode < 0 || $exitCode > 255) {
            throw new Exception("Invalid exit code: ".$exitCode);
        }
        $this->exitCode = $exitCode;
    }

    /**
     * Gets exit code set
     */
    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    /**
     * Sets response body to show in STDERR stream
     *
     * @param string $body
     */
    public function setErrorBody(string $errorBody): void
    {
        $this->errorBody = $errorBody;
    }

    /**
     * Sends response body back to caller
     * 
     * @param string $body
     */
    protected function emit(string $body): void
    {
        if ($body) {
            fwrite(STDOUT, $body);
        }
        if ($this->errorBody) {
            fwrite(STDERR, $this->errorBody);
        }
    }
}