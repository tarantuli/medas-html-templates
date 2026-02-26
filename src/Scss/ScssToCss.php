<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Scss;

use Medas\Core\Attributes\Service;
use ScssPhp\ScssPhp\Compiler;

#[Service]
readonly class ScssToCss
{
    public function __construct(
        private Compiler $compiler = new Compiler(),
    )
    {
    }

    public function convert(string $scss): string
    {
        return $this->compiler->compileString($scss)->getCss();
    }
}
