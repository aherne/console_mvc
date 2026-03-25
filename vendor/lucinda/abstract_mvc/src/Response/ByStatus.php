<?php

namespace Lucinda\MVC\Response;

use Lucinda\MVC\Response;
use Lucinda\MVC\Response\Attachment\Partial;

/**
 * Implements a HTTP status only response with optional body
 */
final class ByStatus implements Response
{
    private HttpStatus $status;
    protected Headers $headers;
    private string $body = null;

    /**
     * Sets HTTP status of response
     * 
     * @param HttpStatus $status
     */
    public function __construct(HttpStatus $status)
    {
        if (in_array($status->value, Blank::SUPPORTED_STATUSES)) {
            throw new Exception("For ".$status->value." http status use this class: ".Blank::class);
        } else if (in_array($status->value, Partial::SUPPORTED_STATUSES)) {
            throw new Exception("For ".$status->value." http status use this class: ".Partial::class);
        } else if (in_array($status->value, Redirect::SUPPORTED_STATUSES)) {
            throw new Exception("For ".$status->value." http status use this class: ".Redirect::class);
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
     * Sets custom body to headers-based resonse
     * 
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * Commits response to client.
     */
    public function run(): void
    {
        http_response_code($this->status->value);
        $this->headers->send();
        if ($this->body!==null) {
            echo $this->body;
        }
        exit;
    }
}