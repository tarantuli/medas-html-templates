<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidTemplateException extends BaseException
{
    public function __construct(string $code)
    {
        parent::__construct($code);
    }

    public function pattern(): string
    {
        return 'Invalid template code: %s';
    }
}
