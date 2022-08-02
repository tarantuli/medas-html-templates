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

    public function testBasicLoopInAttributes(): void
    {
        $template = new HtmlTemplate(
            '<div m-foreach="$numbers" m-as="$number"><a href="{{ $number }}"></a></div>',
            ['numbers' => [1, 2, 3]]
        );

        self::assertEquals(
            '<div><a href="1"></a></div><div><a href="2"></a></div><div><a href="3"></a></div>',
            $this->compile($template)
        );
    }

    public function testReplaceOnlyWithinBraces(): void
    {
        $template = new HtmlTemplate(
            '<div m-foreach="$numbers" m-as="$number">$number = {{ $number }}</div>',
            ['numbers' => [1, 2, 3]]
        );

        self::assertEquals(
            '<div>$number = 1</div><div>$number = 2</div><div>$number = 3</div>',
            $this->compile($template)
        );
    }
}
