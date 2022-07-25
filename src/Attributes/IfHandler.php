<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Attributes;

use Medas\HtmlTemplates\ConfigOptions\AttributePrefix;
use Medas\HtmlTemplates\StringEvaluator;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\{ConfigValue, Service};

#[Service]
class IfHandler extends BaseHandler implements AttributeHandler
{
    private HtmlTemplate $template;

    public function __construct(
        #[ConfigValue(AttributePrefix::class)]
        private readonly string          $prefix,
        private readonly StringEvaluator $stringEvaluator,
    )
    {
    }

    public function name(): string
    {
        return 'if';
    }

    public function priority(): int
    {
        return 2;
    }

    public function handle(HtmlTemplate $template)
    {
        $this->template = $template;
        $this->findAttributes(
            $template->dom,
            $this->prefix . $this->name(),
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
