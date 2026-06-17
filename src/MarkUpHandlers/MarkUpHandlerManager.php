<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\{Attributes\Service, CachedImplementorList, Lists\SortByPriority};

#[Service]
readonly class MarkUpHandlerManager
{
    private CachedImplementorList $cachedImplementorList;

    public function __construct()
    {
        $this->cachedImplementorList = new CachedImplementorList(
            MarkUpHandler::class,
            SortByPriority::HighToLow
        );
    }

    /** @return MarkUpHandler[] */
    public function get(): array
    {
        return $this->cachedImplementorList->get();
    }
}
