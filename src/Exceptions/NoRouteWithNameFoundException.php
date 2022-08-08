<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NoRouteWithNameFoundException extends BaseException
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function pattern(): string
    {
        return 'No routed handler found with name %s';
    }
}
