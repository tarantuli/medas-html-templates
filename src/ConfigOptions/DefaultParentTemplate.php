<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\ConfigOptions;

use Medas\HtmlTemplates\Templates\HasDefaultParentTemplate;
use Medas\ServiceManager\AsSingleton;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\ConfigOptions\{ConfigGroup, ConfigOption};
use Medas\ServiceManager\Values\Interfaces\Unserializer;
use Medas\ServiceManager\Values\Interfaces\Validator;

#[Service]
class DefaultParentTemplate implements ConfigOption, Validator, Unserializer
{
    use AsSingleton;

    public function group(): ConfigGroup
    {
        return Group::instance();
    }

    public function name(): string
    {
        return 'default-parent-template';
    }

    public function description(): string
    {
        return 'The default parent template, used by all templates if no parent is given';
    }

    public function isValid(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_string($value) || !class_exists($value)) {
            return false;
        }

        try {
            $service = \service($value);

            return $service instanceof HasDefaultParentTemplate;
        }
        catch (\Exception) {
            return false;
        }
    }

    public function unserialize(string $value): object
    {
        return \service($value)->defaultParentTemplate();
    }

    public function hasDefault(): bool
    {
        return false;
    }

    public function default(): mixed
    {
        return null;
    }
}
