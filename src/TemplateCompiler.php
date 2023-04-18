<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\Core\Attributes\ConfigValue;
use Medas\Core\Attributes\Service;
use Medas\HtmlTemplates\ConfigOptions\AttributePrefix;
use Medas\HtmlTemplates\ConfigOptions\DefaultParentTemplate;
use Medas\HtmlTemplates\Exceptions\InvalidTemplateException;
use Medas\HtmlTemplates\MarkUpHandlers\MarkUpHandlerManager;
use Medas\HtmlTemplates\Templates\HtmlTemplate;

#[Service]
class TemplateCompiler
{
    public function __construct(
        private readonly MarkUpHandlerManager $markUpHandlerManager,
        #[ConfigValue(DefaultParentTemplate::class)]
        private readonly HtmlTemplate|null    $defaultParent,
        #[ConfigValue(AttributePrefix::class)]
        private readonly string               $prefix,
    )
    {
    }

    public function compile(HtmlTemplate $template): string
    {
        $this->setParentTemplate($template);

        $template->dom = new \DOMDocument();

        $this->loadTemplateXml($template);
        $this->applyMarkUpHandlers($template);

        return $this->getXml($template->dom);
    }

    private function setParentTemplate(HtmlTemplate $template): void
    {
        if ($template->parent === null && $this->defaultParent !== null) {
            $template->parent = $this->defaultParent;
        }
    }

    private function applyMarkUpHandlers(HtmlTemplate $template): void
    {
        foreach ($this->markUpHandlerManager->get() as $markUpHandler) {
            $markUpHandler->handle($template);
        }
    }

    private function loadTemplateXml(HtmlTemplate $template): void
    {
        $template->dom->preserveWhiteSpace = false;
        $template->dom->formatOutput = true;

        try {
            $template->dom->loadXML($template->template);
        }
        catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Extra content at the end of the document')) {
                // Try to make non-rooted code valid by encapsulating all in a temporary root element
                try {
                    $template->dom->loadXML('<' . $this->prefix . 'container>' . $template->template . '</' . $this->prefix . 'container>');
                }
                catch (\Exception) {
                    throw new InvalidTemplateException($template->template);
                }
            }
            else {
                throw new InvalidTemplateException($template->template);
            }
        }
    }

    private function getXml(\DOMDocument $document): string
    {
        $result = '';

        foreach ($document->childNodes as $childNode) {
            $result .= $document->saveXML($childNode, LIBXML_NOEMPTYTAG) . "\n";
        }

        return trim($result);
    }
}
