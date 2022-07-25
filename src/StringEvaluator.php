<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class StringEvaluator
{
    public function evaluate(string $string, array $variables): string
    {
        $replacements = [];

        foreach ($variables as $variable => $value) {
            $replacements['\\$' . $variable] = $value;
        }

        $replacements['\btrue'] = '1';
        $replacements['\bfalse'] = '0';
        $replacements['\bnull'] = '';

        // Replace variables by values
        foreach ($replacements as $variable => $value) {
            if ($value === false) {
                $value = '0';
            }

            if (is_object($value)) {
                $string = preg_replace_callback("/$variable\.(?<property>\w+)/", function ($m) use ($value) {
                    return $value->{$m[1]};
                }, $string);
            }
            else {
                $string = preg_replace("/$variable\b/", (string) $value, $string);
            }
        }

        return $string;
    }

    public function isTruthy(string $string, array $variables): bool
    {
        $value = $this->evaluate($string, $variables);

        if (ctype_digit($value)) {
            return (bool) intval($value);
        }

        if (is_numeric($value)) {
            return !preg_match('/^-?0?\.0+$/', $value);
        }

        return false;
    }
}
