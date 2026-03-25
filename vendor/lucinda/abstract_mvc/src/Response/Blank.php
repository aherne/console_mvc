<?php

namespace Lucinda\MVC\Response;

use Lucinda\MVC\Response;

/**
 * Implements an empty HTTP response (only status and headers)
 */
final class Blank implements Response
{
    const SUPPORTED_STATUSES = [204, 205, 304];
    private HttpStatus $status;
    protected Headers $headers;
    
    public function __construct(HttpStatus $status)
    {
        if (!in_array($status->value, self::SUPPORTED_STATUSES)) {
            throw new Exception(
                "Only these HTTP statuses are supported here: ".
                implode(",", self::SUPPORTED_STATUSES)
                );
        }
        $this->status = $status;
        $this->headers = new Headers();
    }

    /**
     * Sets response header
     *
     * @param string $key
     * @param string|string[] $value
     * @return void
     */
    public function setHeader(string $key, string|array $value): void
    {
        $this->headers->add($key, $value);
    }

    /**
     * Commits response to client.
     */
    public function run(): void
    {
        http_response_code($this->status->value);
        $this->headers->send();
        exit;
    }
}