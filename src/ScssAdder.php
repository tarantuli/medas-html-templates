<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\Core\Attributes\Service;

#[Service]
readonly class ScssAdder
{
    public function __construct(
        private Scss\ScssToCss $scssToCss,
    )
    {
    }

    public function addScss(Templates\HtmlTemplate $template, string $id, string $scss): void
    {
        $template->template = str_replace(
            sprintf('<style id="%s"></style>', $id),
            sprintf("<style>%s</style>", $this->scssToCss->convert($scss)),
            $template->template
        );
    }
}
