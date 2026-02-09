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
class IfHandler extends BaseHandler implements MarkUpHandler
{
    private HtmlTemplate $template;

    public function __construct(
        private readonly StringEvaluator $stringEvaluator,

        #[ConfigValue(AttributePrefix::class)]
        private readonly string          $prefix,
    )
    {
    }

    public function priority(): int
    {
        return -300;
    }

    public function handle(HtmlTemplate $template): void
    {
        $this->template = $template;

        $this->callOnAttributes($template->dom, $this->prefix . 'if', $this->processAttribute(...));
    }

    protected function processAttribute(\DOMElement $element, \DOMAttr $attribute): void
    {
        if ($this->stringEvaluator->isTruthy($attribute->value, $this->template->variables)) {
            $element->removeAttribute($attribute->name);
        }
        else {
            $element->remove();
        }
    }
}
