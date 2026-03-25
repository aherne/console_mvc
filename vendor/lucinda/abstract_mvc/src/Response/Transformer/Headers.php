<?php
namespace Lucinda\MVC\Response\Transformer;

use Lucinda\MVC\Response\Headers as HttpHeaders;
use Lucinda\MVC\Response\HttpStatus;

/**
 * Contains blueprint of extra headers to add
 */
interface Headers extends Transformer
{
    /**
     * Gets extra headers to add
     * 
     * @return HttpHeaders
     */
    function getExtraHeaders(): HttpHeaders;
}
