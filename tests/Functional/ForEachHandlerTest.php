<?php

declare(strict_types=1);

namespace Medas\HtmlTemplatesTest\Functional;

use Medas\HtmlTemplates\Templates\HtmlTemplate;

class ForEachHandlerTest extends BaseTest
{
    public function testNoAsAttribute(): void
    {
        $template = new HtmlTemplate(
            '<div m-foreach="$numbers">{{ $number }}</div>',
            ['numbers' => [1, 2, 3]]
        );

        self::expectException(\Exception::class);
        $this->compile($template);
    }

    public function testExpressionNotIterable(): void
    {
        $template = new HtmlTemplate(
            '<div m-foreach="$numbers" m-as="$number">{{ $number }}</div>',
            ['numbers' => 1]
        );

        self::expectException(\Exception::class);
        $this->compile($template);
    }

    public function testBasicLoop(): void
    {
        $template = new HtmlTemplate(
            '<div m-foreach="$numbers" m-as="$number">{{ $number }}</div>',
            ['numbers' => [1, 2, 3]]
        );

        self::assertEquals('<div>1</div><div>2</div><div>3</div>', $this->compile($template));
    }
}
