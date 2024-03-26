<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
class HtmlTemplatesConfigGroup implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'html-templates';
    }
}
