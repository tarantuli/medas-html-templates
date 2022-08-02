<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ServiceManager\{AsSingleton, BasePackage};
use Medas\Routing\RoutingPackage;

class HtmlTemplatesPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            ConfigOptionsPackage::class,
            RoutingPackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function initialize(): void
    {
        require_once __DIR__ . '/GlobalFunctions.php';
        parent::initialize();
    }
}
