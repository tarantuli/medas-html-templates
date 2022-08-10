<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Exceptions;

use Medas\Core\Exceptions\BaseException;

class MultipleServicesWithShortNameException extends BaseException
{
    public function __construct(string $shortName, array $services)
    {
        parent::__construct($shortName, implode(', ', $services));
    }

    public function pattern(): string
    {
        return 'multiple services found with short name %s: %s';
    }
}
