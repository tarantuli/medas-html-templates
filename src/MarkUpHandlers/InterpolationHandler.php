<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\HtmlTemplates\StringEvaluation\StringEvaluator;
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

    public function priority(): int
    {
        return -400;
    }

    public function handle(HtmlTemplate $template): void
    {
        $this->template = $template;

        $this->processNode($template->dom);
    }

    private function processNode(\DOMNode $node): void
    {
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attribute) {
                $this->parseAttribute($attribute);
            }
        }

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

    private function parseAttribute(\DOMAttr $attribute): void
    {
        if (!preg_match_all('/\{\{(?<expression>.*?)}}/', $attribute->value, $matches, PREG_SET_ORDER)) {
            return;
        }

        $attribute->value = $this->evaluateMatches($matches, $attribute->value);
    }

    private function parseTextNode(\DOMNode $parentNode, \DOMNode $childNode): void
    {
        if (!preg_match_all('/\{\{(?<expression>.*?)}}/', $childNode->textContent, $matches, PREG_SET_ORDER)) {
            return;
        }

        $newText = $this->evaluateMatches($matches, $childNode->textContent);

        $fragment = $parentNode->ownerDocument->createDocumentFragment();
        try {
            $fragment->appendXML($newText);
        }
        catch (\Exception $exception) {
            throw new \Exception($exception->getMessage() . ' in ' . $newText);
        }
        $parentNode->replaceChild($fragment, $childNode);
    }

    private function evaluateMatches($matches, mixed $newText): mixed
    {
        foreach ($matches as $match) {
            $replace = $this->stringEvaluator->evaluate($match['expression'], $this->template->variables);
            $newText = str_replace($match[0], (string) $replace, $newText);
        }

        return $newText;
    }
}
