<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\{ConfigGroup, ConfigOption};

#[Service]
class AttributePrefix implements ConfigOption
{
    public function __construct(
        private readonly Group $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
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
