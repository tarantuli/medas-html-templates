<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\Attributes\Service;
use Medas\HtmlTemplates\Exceptions\PlaceholderTagNotFoundException;
use Medas\HtmlTemplates\Templates\HtmlTemplate;

#[Service]
class ParentHandler implements MarkUpHandler
{
    public function priority(): int
    {
        return 100;
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
            throw new PlaceholderTagNotFoundException($template->parent, $template->parentPlaceholderTag);
        }

        // Import the root element from the original DOM and then replace the placeholder in the parent
        $importedNode = $parentDom->importNode($template->dom->documentElement, true);
        $placeholder->parentNode->replaceChild($importedNode, $placeholder);

        // From now on, use the parent DOM document as the template
        $template->dom = $parentDom;

        // Combine the variables
        $template->variables = array_merge($template->parent->variables, $template->variables);
    }
}
