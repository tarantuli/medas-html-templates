<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\HtmlTemplates\Exceptions\PlaceholderTagNotFoundException;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ParentHandler implements MarkUpHandler
{
    public function attributes(): array
    {
        return [];
    }

    public function priority(): int
    {
        return 0;
    }

    public function handle(HtmlTemplate $template): void
    {
        if (!$template->parent) {
            return;
        }

        // Turn the parent template into a DOM document
        $parentDom = new \DOMDocument();
        $parentDom->loadXML($template->parent->template);

        // Find the placeholder and replace it by the template DOM
        $placeholder = $parentDom->getElementsByTagName($template->parentPlaceholderTag)->item(0);

        if (!$placeholder) {
            throw new PlaceholderTagNotFoundException($template->parentPlaceholderTag);
        }

        $placeholder->parentNode->replaceChild($template->dom, $placeholder);

        // From now on, use the parent DOM document as the template
        $template->dom = $parentDom;
    }
}
