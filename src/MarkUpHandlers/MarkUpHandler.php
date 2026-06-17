<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\Interfaces\DeclaresPriority;
use Medas\HtmlTemplates\Templates\HtmlTemplate;

interface MarkUpHandler extends DeclaresPriority
{
    public function handle(HtmlTemplate $template): void;
}
