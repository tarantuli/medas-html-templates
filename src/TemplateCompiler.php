<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\ConfigOptions\Attributes\ConfigValue;
use Medas\HtmlTemplates\ConfigOptions\DefaultParentTemplate;
use Medas\HtmlTemplates\Exceptions\InvalidTemplateException;
use Medas\HtmlTemplates\MarkUpHandlers\MarkUpHandlerManager;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class TemplateCompiler
{
    public function __construct(
        private readonly MarkUpHandlerManager $markUpHandlerManager,
        #[ConfigValue(DefaultParentTemplate::class)]
        private readonly HtmlTemplate|null    $defaultParent,
    )
    {
    }

    public function compile(HtmlTemplate $template): string
    {
        $this->setParentTemplate($template);

        $template->dom = new \DOMDocument();

        $this->loadTemplateXml($template);
        $this->applyMarkUpHandlers($template);

        return trim($template->dom->saveHTML());
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
        try {
            $template->dom->loadXML($template->template);
        }
        catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Extra content at the end of the document')) {
                // Try to make non-rooted code valid by encapsulating all in a temporary root element
                try {
                    $template->dom->loadXML('<null>' . $template->template . '</null>');
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
}
