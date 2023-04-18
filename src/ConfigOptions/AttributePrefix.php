<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\{ConfigGroup, ConfigOption};
use Medas\ServiceManager\AsSingleton;

#[Service]
class AttributePrefix implements ConfigOption
{
    use AsSingleton;

    public function group(): ConfigGroup
    {
        return Group::instance();
    }

    public function name(): string
    {
        return 'attribute-prefix';
    }

    public function description(): string
    {
        return 'The global prefix used to mark the template specific attributes';
    }

    public function isValid(mixed $value): bool
    {
        return is_string($value) && preg_match('/^\S+$/', $value);
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return 'm-';
    }
}
