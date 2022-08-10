<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NoServiceWithShortNameException extends BaseException
{
    public function __construct(string $shortName)
    {
        parent::__construct($shortName);
    }

    public function pattern(): string
    {
        return 'no service found with short name %s';
    }
}
