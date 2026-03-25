<?php

namespace Lucinda\MVC\Response;

/**
 * Implements a HTTP headers collection
 */
final class Headers
{
    /**
     * @var array<string,string|array>
     */
    private array $headers = [];

    /**
     * Adds header to collection
     * 
     * @param string $key
     * @param string|array $value
     */
    public function add(string $key, string|array $value): void
    {
        if (is_array($value) && (empty($value) || in_array("", $value))) {
            throw new Exception("Multi headers cannot be empty!");
        }
        $this->headers[$key] = $value;
    }

    /**
     * Gets values in collection
     * 
     * @return array<string,string|array>
     */
    public function get(): array
    {
        return $this->headers;
    }

    /**
     * Sends headers to caller
     */
    public function send(): void
    {
        foreach ($this->headers as $name=>$value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    header($name.": ".$item);
                }
            } else {
                header($name.": ".$value);
            }
        }
    }
}

