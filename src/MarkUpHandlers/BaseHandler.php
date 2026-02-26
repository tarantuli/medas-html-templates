<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

readonly abstract class BaseHandler
{
    protected function callOnAttributes(\DOMNode $node, string $name, \Closure $closure): void
    {
        if ($node->hasAttributes() && $attribute = $node->attributes->getNamedItem($name)) {
            $closure($node, $attribute);
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                $this->callOnAttributes($childNode, $name, $closure);
            }
        }
    }

    protected function replaceVariableInText(\DOMNode $node, string $search, string $replace): void
    {
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attribute) {
                $attribute->value = $this->replaceTextInExpressions(
                    $search,
                    $replace,
                    $attribute->value
                );
            }
        }

        if ($node->hasChildNodes()) {
            // Work on a copy of the children due to DOM manipulations
            $children = [];

            foreach ($node->childNodes as $childNode) {
                $children[] = $childNode;
            }

            foreach ($children as $childNode) {
                if ($childNode->nodeType === XML_TEXT_NODE) {
                    $oldText = $childNode->textContent;
                    $newText = $this->replaceTextInExpressions($search, $replace, $oldText);
                    $newTextNode = $node->ownerDocument->createTextNode($newText);

                    $node->replaceChild($newTextNode, $childNode);
                }
                else {
                    $this->replaceVariableInText($childNode, $search, $replace);
                }
            }
        }
    }

    private function replaceTextInExpressions(string $pattern, string $replace, string $subject): string
    {
        if (!preg_match_all('/{{.+?}}/', $subject, $expressions)) {
            return $subject;
        }

        foreach ($expressions[0] as $expression) {
            $subject = str_replace(
                $expression,
                preg_replace($pattern, $replace, $expression),
                $subject
            );
        }

        return $subject;
    }
}
