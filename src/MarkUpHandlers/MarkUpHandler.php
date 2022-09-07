<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\HtmlTemplates\Templates\HtmlTemplate;

interface MarkUpHandler
{
    public function priority(): int;

    public function handle(HtmlTemplate $template): void;
}
