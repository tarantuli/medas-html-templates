<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\Routing\HandlerManager;

function routeTo(string $name, array $arguments = []): string
{
    $handler = service(HandlerManager::class)->findByName($name);

    return $handler->endpoint($arguments);
}
