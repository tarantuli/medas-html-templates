<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\Attributes\ConfigValue;
use Medas\Core\Attributes\Service;
use Medas\HtmlTemplates\ConfigOptions\AttributePrefix;
use Medas\HtmlTemplates\StringEvaluation\StringEvaluator;
use Medas\HtmlTemplates\Templates\HtmlTemplate;

#[Service]
class IfHandler extends BaseHandler implements MarkUpHandler
{
    private HtmlTemplate $template;

    public function __construct(
        #[ConfigValue(AttributePrefix::class)]
        private readonly string          $prefix,
        private readonly StringEvaluator $stringEvaluator,
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

        $this->callOnAttributes(
            $template->dom,
            $this->prefix . 'if',
            $this->processAttribute(...)
        );
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
