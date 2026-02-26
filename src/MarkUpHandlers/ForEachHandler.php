<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\HtmlTemplates\{
    ConfigOptions\AttributePrefix,
    Exceptions\InvalidTemplateException,
    StringEvaluation\StringEvaluator,
    Templates\HtmlTemplate
};

#[Service]
readonly class ForEachHandler extends BaseHandler implements MarkUpHandler
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
        return -200;
    }

    public function handle(HtmlTemplate $template): void
    {
        $this->callOnAttributes(
            $template->dom,
            $this->prefix . 'foreach',
            fn(
                \DOMElement $element,
                \DOMAttr $attribute) => $this->processAttribute($template,
                $element,
                $attribute
            )
        );
    }

    protected function processAttribute(
        HtmlTemplate $template,
        \DOMElement  $element,
        \DOMAttr     $forEachAttribute
    ): void
    {
        /** @var \DOMAttr $asAttribute */
        $asAttribute = $element->attributes->getNamedItem($this->prefix . 'as');

        if (!$asAttribute) {
            throw new InvalidTemplateException('Element with ' . $this->prefix . 'foreach is missing required ' . $this->prefix . 'as attribute');
        }

        $iterator = $this->stringEvaluator->evaluate(
            $forEachAttribute->value,
            $template->variables
        );

        if (!is_iterable($iterator)) {
            throw new InvalidTemplateException($this->prefix . 'foreach attribute "' . $forEachAttribute->value . '" does not resolve to an iterable');
        }

        $search = '/' . preg_quote($asAttribute->value, '/') . '\b/';
        $tempVarNames = [];

        foreach ($iterator as $value) {
            $variableName = 'a' . bin2hex(random_bytes(8));
            $tempVarNames[] = $variableName;
            $template->variables[$variableName] = $value;
            $newBlock = $element->cloneNode(true);

            $newBlock->removeAttribute($this->prefix . 'foreach');
            $newBlock->removeAttribute($this->prefix . 'as');

            $this->replaceVariableInText($newBlock, $search, '$' . $variableName);

            $element->parentNode->insertBefore($newBlock, $element);
        }

        $element->remove();

        // Clean up temporary loop variables to prevent leakage into later handlers
        foreach ($tempVarNames as $tempVarName) {
            unset($template->variables[$tempVarName]);
        }
    }
}
