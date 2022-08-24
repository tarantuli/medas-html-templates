<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\HtmlTemplates\Exceptions\NoRouteWithNameFoundException;
use Medas\ServiceManager\RequestHandling\{GeneratesEndpoint, RequestHandlerManager};

function routeTo(string $name, array $arguments = []): string
{
    $handler = service(RequestHandlerManager::class)->findByName($name);

    if ($handler instanceof GeneratesEndpoint) {
        return $handler->endpoint($arguments);
    }

    throw new NoRouteWithNameFoundException($name);
}
