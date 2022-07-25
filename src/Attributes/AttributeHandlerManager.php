<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Attributes;

use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Cache\CacheManager;

#[Service]
class AttributeHandlerManager
{
    /** @var AttributeHandler[] $handlers */
    private array $handlers;

    public function __construct(
        private readonly CacheManager $cacheManager,
    )
    {
    }

    /** @return AttributeHandler[] */
    public function get(): array
    {
        return $this->cacheManager->get()->get([static::class, 'getHandlers'], fn() => $this->findHandlers());
    }

    private function findHandlers(): array
    {
        $this->handlers = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $this->processClass($className);
        }

        // Sort handlers with the highest priority to the front
        usort($this->handlers, fn(AttributeHandler $a, AttributeHandler $b) => -($a->priority() <=> $b->priority()));

        return $this->handlers;
    }

    private function processClass(string $className): void
    {
        $class = new \ReflectionClass($className);

        if ($class->isAbstract()) {
            return;
        }

        if (!$class->implementsInterface(AttributeHandler::class)) {
            return;
        }

        $this->handlers[] = sm()->resolve($className);
    }
}
