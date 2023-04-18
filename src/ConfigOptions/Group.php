<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\ConfigGroup;
use Medas\ServiceManager\AsSingleton;

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
