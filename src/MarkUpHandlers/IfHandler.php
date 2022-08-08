<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\ConfigOptions\Attributes\ConfigValue;
use Medas\HtmlTemplates\ConfigOptions\AttributePrefix;
use Medas\HtmlTemplates\StringEvaluator;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\Service;

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

    public function attributes(): array
    {
        return [$this->prefix . 'if'];
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
