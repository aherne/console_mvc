<?php

namespace Lucinda\MVC;

interface RequestValidator
{
    function getRoute(): string;
    function getFormat(): string;
}