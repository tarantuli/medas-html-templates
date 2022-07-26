<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Attributes;

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
}
