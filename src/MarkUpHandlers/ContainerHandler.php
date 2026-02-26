<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\MarkUpHandlers;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\HtmlTemplates\{ConfigOptions\AttributePrefix, Templates\HtmlTemplate};

#[Service]
readonly class ContainerHandler extends BaseHandler implements MarkUpHandler
{
    public function __construct(
        #[ConfigValue(AttributePrefix::class)]
        private string $prefix,
    )
    {
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
