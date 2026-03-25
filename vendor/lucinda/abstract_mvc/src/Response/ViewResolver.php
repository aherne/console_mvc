<?php
namespace Lucinda\MVC\Response;

/**
 * Contains the blueprint of view resolving
 */
interface ViewResolver extends Resolver
{
    /**
     * Resolves view into response body
     * 
     * @param View $view
     * @return string
     */
    function resolve(View $view): string;
}
