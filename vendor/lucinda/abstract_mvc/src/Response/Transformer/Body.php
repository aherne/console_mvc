<?php
namespace Lucinda\MVC\Response\Transformer;

/**
 * Contains the blueprint of a post-resolution response body transformer
 */
interface Body extends Transformer
{
    /**
     * Transforms response string into another string
     * 
     * @param string $source
     * @return string
     */
    function transform(string $source): string;
}
