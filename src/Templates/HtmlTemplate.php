<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Templates;

class HtmlTemplate
{
    public \DOMDocument $dom;

    public function __construct(
        public string            $template,
        public array             $variables = [],
        public Settings|null     $settings = null,
        public HtmlTemplate|null $parent = null,
        public string $parentPlaceholderTag = 'children'
    )
    {
    }
}
