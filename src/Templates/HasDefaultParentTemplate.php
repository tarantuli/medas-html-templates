<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\Templates;

interface HasDefaultParentTemplate
{
    public function defaultParentTemplate(): HtmlTemplate;
}
