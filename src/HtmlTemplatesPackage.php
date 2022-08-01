<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ServiceManager\{AsSingleton, BasePackage};

class HtmlTemplatesPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            ConfigOptionsPackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
