<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Templates;

class HtmlTemplate
{
    public \DOMDocument|null $dom = null;

    public function __construct(
        public string            $template,
        public array             $variables = [],
        public HtmlTemplate|null $parent = null,
        public string            $parentPlaceholderTag = 'children'
    )
    {
    }
}
