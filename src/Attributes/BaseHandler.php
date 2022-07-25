<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Attributes;

abstract class BaseHandler
{
    protected function findAttributes(\DOMNode $node, string $name, \Closure $closure): void
    {
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attribute) {
                if ($attribute->name === $name) {
                    $closure($node, $attribute);
                }
            }
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                $this->findAttributes($childNode, $name, $closure);
            }
        }
    }
}
