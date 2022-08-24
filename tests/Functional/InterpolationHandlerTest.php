<?php

declare(strict_types=1);

namespace Medas\HtmlTemplatesTest\Functional;

use Medas\HtmlTemplates\Templates\HtmlTemplate;

class InterpolationHandlerTest extends BaseTest
{
    public function testBasicTextNode(): void
    {
        $template = new HtmlTemplate(
            '<div>{{ $variable }}</div>',
            ['variable' => 'test']
        );

        self::assertEquals('<div>test</div>', $this->compile($template));
    }

    public function testComplexTextNode(): void
    {
        $template = new HtmlTemplate(
            '<div>space after {{ $variable }}no space before</div>',
            ['variable' => 'test']
        );

        self::assertEquals('<div>space after testno space before</div>', $this->compile($template));
    }

    public function testAttribute(): void
    {
        $template = new HtmlTemplate(
            '<a href="{{ $link }}"></a>',
            ['link' => '/path/to/site']
        );

        self::assertEquals('<a href="/path/to/site"></a>', $this->compile($template));
    }
}
