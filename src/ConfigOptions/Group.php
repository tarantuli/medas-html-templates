<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\ConfigOptions;

use Medas\ConfigOptions\{ConfigGroup};
use Medas\ServiceManager\AsSingleton;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Group implements ConfigGroup
{
    use AsSingleton;

    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'html-templates';
    }
}
