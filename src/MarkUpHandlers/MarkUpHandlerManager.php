<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Cache\CacheManager;

#[Service]
class MarkUpHandlerManager
{
    /** @var MarkUpHandler[] $handlers */
    private array $handlers;

    public function __construct(
        private readonly CacheManager $cacheManager,
    )
    {
    }

    /** @return MarkUpHandler[] */
    public function get(): array
    {
        return $this->cacheManager->get()->get([static::class, 'getHandlers'], fn() => $this->findHandlers());
    }

    public function getCustomAttributes(): array
    {
        $attributes = [];

        foreach ($this->get() as $handler) {
            $attributes = array_merge($attributes, $handler->attributes());
        }

        return $attributes;
    }

    private function findHandlers(): array
    {
        $this->handlers = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $this->processClass($className);
        }

        // Sort handlers with the highest priority to the front
        usort($this->handlers, fn(MarkUpHandler $a, MarkUpHandler $b) => -($a->priority() <=> $b->priority()));

        return $this->handlers;
    }

    private function processClass(string $className): void
    {
        $class = new \ReflectionClass($className);

        if ($class->isAbstract()) {
            return;
        }

        if (!$class->implementsInterface(MarkUpHandler::class)) {
            return;
        }

        $this->handlers[] = sm()->resolve($className);
    }
}
