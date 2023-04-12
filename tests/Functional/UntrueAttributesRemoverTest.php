<?php

declare(strict_types=1);

namespace Medas\HtmlTemplatesTest\Functional;

use Medas\HtmlTemplates\Templates\HtmlTemplate;

class UntrueAttributesRemoverTest extends BaseTestClass
{
    public function testRemoveUntrueChecked(): void
    {
        $template = new HtmlTemplate('<input checked=""/>');

        self::assertEquals('<input></input>', $this->compile($template));
    }

    public function testLeaveChecked(): void
    {
        $template = new HtmlTemplate('<input checked="checked"/>');

        self::assertEquals('<input checked="checked"></input>', $this->compile($template));
    }

    public function testLeaveTrueChecked(): void
    {
        $template = new HtmlTemplate('<input checked="{{$true}}"/>', ['true' => true]);

        self::assertEquals('<input checked="checked"></input>', $this->compile($template));
    }
}
