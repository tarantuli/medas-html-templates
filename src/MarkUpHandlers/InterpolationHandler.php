<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\HtmlTemplates\StringEvaluator;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class InterpolationHandler implements MarkUpHandler
{
    private HtmlTemplate $template;

    public function __construct(
        private readonly StringEvaluator $stringEvaluator,
    )
    {
    }

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
        $this->template = $template;

        $this->processNode($template->dom);
    }

    private function processNode(\DOMNode $node): void
    {
        if (!$node->hasChildNodes()) {
            return;
        }

        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType === XML_TEXT_NODE) {
                $this->parseTextNode($node, $childNode);
            }
            else {
                $this->processNode($childNode);
            }
        }

    }

    private function parseTextNode(\DOMNode $parentNode, \DOMNode $childNode)
    {
        if (!preg_match_all('/\{\{(?<expression>.*?)}}/', $childNode->textContent, $matches, PREG_SET_ORDER)) {
            return;
        }

        $newText = $childNode->textContent;

        foreach ($matches as $match) {
            $replace = $this->stringEvaluator->evaluate($match['expression'], $this->template->variables);
            $newText = str_replace($match[0], (string) $replace, $newText);
        }

        $newTextNode = $parentNode->ownerDocument->createTextNode($newText);
        $parentNode->replaceChild($newTextNode, $childNode);
    }
}
