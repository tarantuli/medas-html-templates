<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\HtmlTemplates\MarkUpHandlers\MarkUpHandlerManager;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class TemplateCompiler
{
    public function __construct(
        private readonly MarkUpHandlerManager $markUpHandlerManager,
    )
    {
    }

    public function compile(HtmlTemplate $template): string
    {
        $template->dom = new \DOMDocument();
        $template->dom->loadXML($template->template);

        foreach ($this->markUpHandlerManager->get() as $markUpHandler) {
            $markUpHandler->handle($template);
        }

        return trim($template->dom->saveHTML());
    }
}
