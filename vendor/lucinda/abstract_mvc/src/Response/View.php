<?php

namespace Lucinda\MVC\Response;

/**
 * Compiles criterias that will be used in generating response body
 */
final class View
{
    private string $file = "";
    /**
     * @var array<int|string,mixed>
     */
    private array $data = [];

    /**
     * Constructs view
     * 
     * @param array $data
     * @param ?string $file
     */
    public function __construct(array $data, ?string $file = null)
    {
        $this->setData($data);
        if ($file!==null) {
            $this->setFile($file);
        }
    }

    /**
     * Sets path to template that will be the foundation of view
     *
     * @param string $path
     */
    public function setFile(string $path): void
    {
        $this->file = $path;
    }

    /**
     * Gets path to template that will be the foundation of view
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
    
    /**
     * Set data that will be bound to template or will become the view itself
     * 
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Gets data that will be bound to template or will become the view itself.
     *
     * @return array<int|string,mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
