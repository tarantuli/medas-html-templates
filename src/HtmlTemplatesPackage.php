<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\ServiceManager\{AsSingleton, BasePackage};
use Medas\ConfigManager\ConfigManagerPackage;

class HtmlTemplatesPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            ConfigManagerPackage::class
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
