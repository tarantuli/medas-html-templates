<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\Attributes\Service;
use Medas\HtmlTemplates\{StringEvaluation\StringEvaluator, Templates\HtmlTemplate};

#[Service]
readonly class InterpolationHandler implements MarkUpHandler
{
    public function __construct(
        private StringEvaluator $stringEvaluator,
    )
    {
    }

    public function priority(): int
    {
        return -400;
    }

    public function handle(HtmlTemplate $template): void
    {
        $this->processNode($template->dom, $template);
    }

    private function processNode(\DOMNode $node, HtmlTemplate $template): void
    {
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attribute) {
                $this->parseAttribute($attribute, $template->variables);
            }
        }

        if (!$node->hasChildNodes()) {
            return;
        }

        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType === XML_TEXT_NODE) {
                $this->parseTextNode($node, $childNode, $template->variables);
            }
            else {
                $this->processNode($childNode, $template);
            }
        }
    }

    private function parseTextNode(\DOMNode $parentNode, \DOMNode $childNode, array $variables): void
    {
        if (!preg_match_all('/\{\{(?<expression>.*?)}}/', $childNode->textContent, $matches, PREG_SET_ORDER)) {
            return;
        }

        $newText = $this->evaluateMatches($matches, $childNode->textContent, $variables);
        $fragment = $parentNode->ownerDocument->createDocumentFragment();

        try {
            $fragment->appendXML($newText);
        }
        catch (\Exception $exception) {
            throw new \Exception($exception->getMessage() . ' in ' . $newText);
        }

        $parentNode->replaceChild($fragment, $childNode);
    }

    /**
     * Parses an attribute in place, evaluating any {{ expression }} interpolations.
     * Called externally by UntrueAttributesRemover.
     */
    public function parseAttribute(\DOMAttr $attribute, array $variables = []): void
    {
        if (!preg_match_all('/\{\{(?<expression>.*?)}}/', $attribute->value, $matches, PREG_SET_ORDER)) {
            return;
        }

        $attribute->value = $this->evaluateMatches($matches, $attribute->value, $variables);
    }

    /** @param array<int, array<string, string>> $matches */
    private function evaluateMatches(array $matches, string $text, array $variables): string
    {
        foreach ($matches as $match) {
            $replace = $this->stringEvaluator->evaluate($match['expression'], $variables);
            $text = str_replace($match[0], (string) $replace, $text);
        }

        return $text;
    }
}
