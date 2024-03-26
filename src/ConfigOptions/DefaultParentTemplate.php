<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\ConfigOptions;

use Medas\Core\{
    Attributes\Service,
    Interfaces\ConfigGroup,
    Interfaces\ConfigOption,
    Interfaces\Validator
};
use Medas\HtmlTemplates\Templates\HasDefaultParentTemplate;

#[Service]
class DefaultParentTemplate implements ConfigOption, Validator
{
    public function __construct(
        private readonly HtmlTemplatesConfigGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
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
            $service = service($value);

            return $service instanceof HasDefaultParentTemplate;
        }
        catch (\Exception) {
            return false;
        }
    }

    public function unserialize(string $value): object
    {
        return service($value)->defaultParentTemplate();
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
