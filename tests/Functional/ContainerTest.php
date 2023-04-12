<?php

declare(strict_types=1);

namespace Medas\HtmlTemplatesTest\Functional;

use Medas\HtmlTemplates\Templates\HtmlTemplate;

class ContainerTest extends BaseTestClass
{
    public function testContainerRemoval(): void
    {
        $template = new HtmlTemplate(<<<'HTML'
<div>
    <m-container>
        <h1>Hi</h1>    
    </m-container>
</div>
HTML
        );

        $expected = <<<'HTML'
<div>
  <h1>Hi</h1>
</div>
HTML;

        self::assertEquals($expected, $this->compile($template));
    }

    public function testTempContainerRemoval(): void
    {
        $template = new HtmlTemplate(<<<'HTML'
<h1>Template without root</h1>
<p>Secondary child</p>
HTML
        );

        $expected = <<<'HTML'
<h1>Template without root</h1>
<p>Secondary child</p>
HTML;

        self::assertEquals($expected, $this->compile($template));
    }
}
