<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\HtmlTemplates\ConfigOptions\AttributePrefix;
use Medas\HtmlTemplates\StringEvaluator;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\ConfigValue;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ForEachHandler extends BaseHandler implements MarkUpHandler
{
    private HtmlTemplate $template;

    public function __construct(
        #[ConfigValue(AttributePrefix::class)]
        private readonly string               $prefix,
        private readonly StringEvaluator      $stringEvaluator,
        private readonly MarkUpHandlerManager $markUpHandlerManager,
    )
    {
    }

    public function attributes(): array
    {
        return [$this->prefix . 'foreach', $this->prefix . 'as'];
    }

    public function priority(): int
    {
        return 3;
    }

    public function handle(HtmlTemplate $template)
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
        $customAttributes = $this->markUpHandlerManager->getCustomAttributes();

        foreach ($iterator as $value) {
            $variableName = 'a' . bin2hex(random_bytes(8));
            $this->template->variables[$variableName] = $value;

            $newBlock = $element->cloneNode(true);
            $newBlock->removeAttribute($this->prefix . 'foreach');
            $newBlock->removeAttribute($this->prefix . 'as');

            $this->replaceVariableInText(
                $newBlock,
                $search,
                '$' . $variableName,
                $customAttributes
            );

            $element->parentNode->insertBefore($newBlock, $element);
        }

        $element->remove();
    }
}
