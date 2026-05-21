<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
readonly class MarkUpHandlerManager
{
    public function __construct(
        private CacheManager      $cacheManager,
        private ImplementorFinder $implementorFinder,
    )
    {
    }

    /** @return MarkUpHandler[] */
    public function get(): array
    {
        return $this->cacheManager->get()->get(
            [static::class, 'getHandlers'],
            fn() => $this->findHandlers()
        );
    }

    private function findHandlers(): array
    {
        $handlers = $this->implementorFinder->find(MarkUpHandler::class);

        // Sort handlers with the highest priority to the front
        usort(
            $handlers,
            fn(MarkUpHandler $a, MarkUpHandler $b) => -($a->priority() <=> $b->priority())
        );

        return $handlers;
    }
}
