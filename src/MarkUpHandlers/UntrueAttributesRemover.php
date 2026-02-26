<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\Attributes\Service;
use Medas\HtmlTemplates\Templates\HtmlTemplate;

#[Service]
readonly class UntrueAttributesRemover implements MarkUpHandler
{
    public function __construct(
        private InterpolationHandler $interpolationHandler,
    )
    {
    }

    public function priority(): int
    {
        return -600;
    }

    public function handle(HtmlTemplate $template): void
    {
        $this->processNode($template->dom, $template->variables);
    }

    private function processNode(\DOMNode $node, array $variables): void
    {
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attribute) {
                $this->parseAttribute($attribute, $variables);
            }
        }

        if (!$node->hasChildNodes()) {
            return;
        }

        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType !== XML_TEXT_NODE) {
                $this->processNode($childNode, $variables);
            }
        }
    }

    private function parseAttribute(\DOMAttr $attribute, array $variables): void
    {
        if (!in_array($attribute->name, ['checked', 'selected'])) {
            return;
        }

        $this->interpolationHandler->parseAttribute($attribute, $variables);

        if (!$attribute->value) {
            $attribute->ownerElement->removeAttribute($attribute->name);
        }
        else {
            $attribute->value = $attribute->name;
        }
    }
}
