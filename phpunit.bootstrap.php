<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\HtmlTemplates\HtmlTemplatesPackage;
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\ServiceManager\{ServiceConfigBuilder, ServiceManager};

chdir(__DIR__);

require_once 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfigBuilder {
    $config = new ServiceConfigBuilder(ObjectInstantiator::class);

    $config->addPackages([
        HtmlTemplatesPackage::instance(),
        ConfigOptionsPackage::instance(),
        ConfigManagerPackage::instance(),
    ]);

    return $config;
});
