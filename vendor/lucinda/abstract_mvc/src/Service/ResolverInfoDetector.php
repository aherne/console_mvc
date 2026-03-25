<?php

namespace Lucinda\MVC\Service;

use Lucinda\MVC\ConfigurationException;
use Lucinda\MVC\Application;
use Lucinda\MVC\Facets\ResolverInfo;
use Lucinda\MVC\RequestValidator;

final class ResolverInfoDetector
{
    private ResolverInfo $resolver;
    
    public function __construct(
        Application $application,
        RequestValidator $validatedRequest
    )
    {
        $this->setResolver($application, $validatedRequest);
    }

    private function setResolver(
        Application $application,
        RequestValidator $validatedRequest
    ): void
    {
        $resolver = $application->getResolvers($validatedRequest->getFormat());
        if ($resolver === null) {
            throw new ConfigurationException("Resolver not set for: ".$validatedRequest->getFormat());
        }
        $this->resolver = $resolver;
    }

    public function getResolver(): ResolverInfo
    {
        return $this->resolver;
    }
}