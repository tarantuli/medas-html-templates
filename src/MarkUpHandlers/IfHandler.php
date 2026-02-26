<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\HtmlTemplates\{
    ConfigOptions\AttributePrefix,
    StringEvaluation\StringEvaluator,
    Templates\HtmlTemplate
};

#[Service]
readonly class IfHandler extends BaseHandler implements MarkUpHandler
{
    public function __construct(
        private StringEvaluator $stringEvaluator,

        #[ConfigValue(AttributePrefix::class)]
        private string          $prefix,
    )
    {
    }

    public function priority(): int
    {
        return -300;
    }

    public function handle(HtmlTemplate $template): void
    {
        $this->callOnAttributes(
            $template->dom,
            $this->prefix . 'if',
            fn(
                \DOMElement $element,
                \DOMAttr $attribute) => $this->processAttribute($template,
                $element,
                $attribute
            )
        );
    }

    protected function processAttribute(HtmlTemplate $template, \DOMElement $element, \DOMAttr $attribute): void
    {
        if ($this->stringEvaluator->isTruthy($attribute->value, $template->variables)) {
            $element->removeAttribute($attribute->name);
        }
        else {
            $element->remove();
        }
    }
}
