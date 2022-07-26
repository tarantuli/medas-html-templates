<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class StringEvaluator
{
    public function evaluate(string $expression, array $variables = []): mixed
    {
        // Define the variables in this local scope
        foreach ($variables as $name => $value) {
            $$name = $value;
        }

        return eval('return ' . $expression . ';');
    }

    public function isTruthy(string $string, array $variables = []): bool
    {
        return (bool) $this->evaluate($string, $variables);
    }
}
