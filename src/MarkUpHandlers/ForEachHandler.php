<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\HtmlTemplates\ConfigOptions\AttributePrefix;
use Medas\HtmlTemplates\StringEvaluation\StringEvaluator;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\ConfigOptions\ConfigValue;

#[Service]
class ForEachHandler extends BaseHandler implements MarkUpHandler
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
        return -200;
    }

    public function handle(HtmlTemplate $template): void
    {
        $this->template = $template;
        $this->callOnAttributes(
            $template->dom,
            $this->prefix . 'foreach',
            $this->processAttribute(...)
        );
    }

    protected function processAttribute(\DOMElement $element, \DOMAttr $forEachAttribute): void
    {
        /** @var \DOMAttr $asAttribute */
        $asAttribute = $element->attributes->getNamedItem($this->prefix . 'as');

        if (!$asAttribute) {
            throw new \Exception('no w-as attribute found');
        }

        $iterator = $this->stringEvaluator->evaluate($forEachAttribute->value, $this->template->variables);

        if (!is_iterable($iterator)) {
            throw new \Exception('foreach attribute does not resolve to an iterable result');
        }

        $search = '/' . preg_quote($asAttribute->value, '/') . '\b/';

        foreach ($iterator as $value) {
            $variableName = 'a' . bin2hex(random_bytes(8));
            $this->template->variables[$variableName] = $value;

            $newBlock = $element->cloneNode(true);
            $newBlock->removeAttribute($this->prefix . 'foreach');
            $newBlock->removeAttribute($this->prefix . 'as');

            $this->replaceVariableInText(
                $newBlock,
                $search,
                '$' . $variableName
            );

            $element->parentNode->insertBefore($newBlock, $element);
        }

        $element->remove();
    }
}
