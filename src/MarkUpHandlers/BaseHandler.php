<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

abstract class BaseHandler
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

    protected function replaceVariableInText(\DOMNode $node, string $search, string $replace, array $attributeNames = []): void
    {
        if ($node->hasAttributes()) {
            foreach ($attributeNames as $attributeName) {
                /** @var \DOMAttr $attribute */
                if ($attribute = $node->attributes->getNamedItem($attributeName)) {
                    $attribute->value = preg_replace($search, $replace, $attribute->value);
                }
            }
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType === XML_TEXT_NODE) {
                    $oldText = $childNode->textContent;
                    $newText = preg_replace($search, $replace, $oldText);
                    $newTextNode = $node->ownerDocument->createTextNode($newText);
                    $node->replaceChild($newTextNode, $childNode);
                }
                else {
                    $this->replaceVariableInText($childNode, $search, $replace, $attributeNames);
                }
            }
        }
    }
}
