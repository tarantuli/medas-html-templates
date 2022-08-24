<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\HtmlTemplates\HtmlTemplatesPackage;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

require_once 'vendor/autoload.php';

ServiceManager::get()
    ->addPackages([
        HtmlTemplatesPackage::instance(),
        ConfigOptionsPackage::instance(),
        ConfigManagerPackage::instance(),
    ]);
