<?php

namespace Lucinda\MVC\Response;

use Lucinda\MVC\Response;
use Lucinda\MVC\Response\Transformer\Body as BodyTransformer;

/**
 * Compiles information about a basic string based response
 */
abstract class Basic implements Response
{
    private ?string $body = null;

    /**
     * Sets response body
     *
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
    
    /**
     * Resolves view into response body
     * 
     * @param View $view
     * @param ViewResolver $resolver
     */
    public function resolve(View $view, ViewResolver $resolver): void
    {
        if ($this->body!==null) {
            throw new Exception("Response output stream has already been written to");
        }
        $this->body = $resolver->resolve($view);
    }
    
    /**
     * Applies transformation on response body without exposing the Response object
     * 
     * @param BodyTransformer $transformer
     */
    public function transformBody(BodyTransformer $transformer): void
    {
        if ($this->body===null) {
            throw new Exception("Response output stream has not been written to");
        }
        $this->body = $transformer->transform($this->body);
    }

    /**
     * Commits response to client.
     */
    public function run(): void
    {
        $this->emit($this->body??"");
    }

    /**
     * Sends response body back to caller
     * 
     * @param string $body
     */
    abstract protected function emit(string $body): void;
}
