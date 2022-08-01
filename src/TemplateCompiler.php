<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\ConfigOptions\Attributes\ConfigValue;
use Medas\HtmlTemplates\ConfigOptions\DefaultParentTemplate;
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
        $template->dom->loadXML($template->template);

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
}
