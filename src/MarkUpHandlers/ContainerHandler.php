<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\ConfigOptions\Attributes\ConfigValue;
use Medas\HtmlTemplates\ConfigOptions\AttributePrefix;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ContainerHandler extends BaseHandler implements MarkUpHandler
{
    public function __construct(
        #[ConfigValue(AttributePrefix::class)]
        private readonly string $prefix,
    )
    {
    }

    public function attributes(): array
    {
        return [];
    }

    public function priority(): int
    {
        return -500;
    }

    public function handle(HtmlTemplate $template): void
    {
        $containers = $template->dom->getElementsByTagName($this->prefix . 'container');

        while ($containers->length > 0) {
            $container = $containers->item(0);
            $fragment = $template->dom->createDocumentFragment();

            while ($container->childNodes->length > 0) {
                $fragment->appendChild($container->childNodes->item(0));
            }

            $container->parentNode->replaceChild($fragment, $container);
        }
    }
}
