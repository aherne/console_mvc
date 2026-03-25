<?php
namespace Lucinda\MVC\Response\Transformer;

use Lucinda\MVC\Response\HttpStatus;

/**
 * Contains the blueprint of a HTTP status to send
 */
interface Status extends Transformer
{
    /**
     * Gets HTTP status to send
     */
    function getHttpStatus(): HttpStatus;
}
