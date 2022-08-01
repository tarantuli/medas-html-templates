<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PlaceholderTagNotFoundException extends BaseException
{
    public function __construct(string $placeholderTag)
    {
        parent::__construct($placeholderTag);
    }

    public function pattern(): string
    {
        return 'parent template does not contain placeholder tag "%s"';
    }
}
