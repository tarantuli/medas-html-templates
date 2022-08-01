<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\HtmlTemplates\Templates\HtmlTemplate;

class PlaceholderTagNotFoundException extends BaseException
{
    public function __construct(HtmlTemplate $parent, string $placeholderTag)
    {
        parent::__construct($parent::class, $placeholderTag);
    }

    public function pattern(): string
    {
        return 'parent template %s does not contain placeholder tag "%s"';
    }
}
