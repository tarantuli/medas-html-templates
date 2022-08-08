<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\HtmlTemplates\Exceptions\NoRouteWithNameFoundException;
use Medas\Routing\{HandlerManager, Handlers\RoutedHandler};

function routeTo(string $name, array $arguments = []): string
{
    $handler = service(HandlerManager::class)->findByName($name);

    if ($handler instanceof RoutedHandler) {
        $handler->endpoint($arguments);
    }

    throw new NoRouteWithNameFoundException($name);
}
