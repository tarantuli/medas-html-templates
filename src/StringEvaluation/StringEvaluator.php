<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\StringEvaluation;

use Medas\Core\Attributes\Service;

#[Service]
readonly class StringEvaluator
{
    public function __construct(
        private ServiceReferenceNormalizer $serviceReferenceNormalizer,
    )
    {
    }

    public function isTruthy(string $string, array $variables = []): bool
    {
        return (bool) $this->evaluate($string, $variables);
    }

    /**
     * Evaluates a PHP expression string in a sandboxed local scope.
     *
     * ⚠ SECURITY WARNING: This method uses eval(). Template expressions and
     * variables MUST originate from trusted sources (i.e., developer-written
     * templates and server-side data only). Never pass user-supplied strings
     * directly as expressions or variable values without sanitization.
     */
    public function evaluate(string $expression, array $variables = []): mixed
    {
        $expression = $this->serviceReferenceNormalizer->normalize($expression);

        // Define the variables in this local scope
        foreach ($variables as $name => $value) {
            $ $name = $value;
        }

        return eval('return ' . $expression . ';');
    }
}
