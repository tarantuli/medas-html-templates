<?php

declare(strict_types=1);

namespace Medas\HtmlTemplatesTest\Functional;

use Medas\HtmlTemplates\Templates\HtmlTemplate;

class IfHandlerTest extends BaseTestClass
{
    public function testBasicIfTrueStatement(): void
    {
        $template = new HtmlTemplate(
            '<div m-if="$cheese">cheese is true</div>',
            ['cheese' => true]
        );

        self::assertEquals('<div>cheese is true</div>', $this->compile($template));
    }

    public function testBasicIfFalseStatement(): void
    {
        $template = new HtmlTemplate(
            '<div m-if="$cheese">cheese is true</div>',
            ['cheese' => false]
        );

        self::assertEquals('', $this->compile($template));
    }
}
