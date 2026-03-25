<?php

namespace Lucinda\MVC\Response;

use Lucinda\MVC\Response\Attachment\Partial;
use Lucinda\MVC\Response\Transformer\Status as HttpStatusTransformer;
use Lucinda\MVC\Response\Transformer\Headers as HttpHeadersTransformer;

/**
 * Compiles information about an a basic http response
 */
abstract class Http extends Basic
{
    private ?HttpStatus $status = null;
    private ?Headers $headers = null;

    public function __construct()
    {
        $this->headers = new Headers();
    }

    /**
     * Sets HTTP response status by its numeric code.
     *
     * @param HttpStatus $status
     */
    public function setStatus(HttpStatus $status): void
    {
        if (in_array($status->value, Blank::SUPPORTED_STATUSES)) {
            throw new Exception("For ".$status->value." http status use this class: ".Blank::class);
        } else if (in_array($status->value, Partial::SUPPORTED_STATUSES)) {
            throw new Exception("For ".$status->value." http status use this class: ".Partial::class);
        } else if (in_array($status->value, Redirect::SUPPORTED_STATUSES)) {
            throw new Exception("For ".$status->value." http status use this class: ".Redirect::class);
        }
        $this->status = $status;
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
        // sends headers
        if (headers_sent()) {
            throw new Exception("Headers were sent already!");
        }

        if ($this->status!==null) {
            http_response_code($this->status->value);
        }
        
        $this->headers->send();

        // displays body
        parent::run();
    }

    /**
     * Sends response body back to caller
     * 
     * @param string $body
     */
    protected function emit(string $body): void
    {
        echo $body;
    }
}