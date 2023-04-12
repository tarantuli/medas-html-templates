<?php

declare(strict_types=1);

namespace Medas\HtmlTemplatesTest\Functional;

use Medas\HtmlTemplates\TemplateCompiler;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    protected function compile(HtmlTemplate $template): string
    {
        return service(TemplateCompiler::class)->compile($template);
    }
}
