<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Attributes;

use Medas\HtmlTemplates\Templates\HtmlTemplate;

interface AttributeHandler
{
    public function name(): string;

    public function priority(): int;

    public function handle(HtmlTemplate $template);
}
