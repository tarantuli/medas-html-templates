<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\StringEvaluation;

use Medas\ServiceManager\Service;

#[Service]
class StringEvaluator
{
    public function __construct(
        private readonly ServiceReferenceNormalizer $serviceReferenceNormalizer,
    )
    {
    }

    public function isTruthy(string $string, array $variables = []): bool
    {
        return (bool) $this->evaluate($string, $variables);
    }

    public function evaluate(string $expression, array $variables = []): mixed
    {
        $expression = $this->serviceReferenceNormalizer->normalize($expression);

        // Define the variables in this local scope
        foreach ($variables as $name => $value) {
            $$name = $value;
        }

        return eval('return ' . $expression . ';');
    }
}
