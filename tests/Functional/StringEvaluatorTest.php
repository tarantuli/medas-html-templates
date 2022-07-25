<?php

declare(strict_types=1);

namespace Medas\HtmlTemplatesTest\Functional;

use Medas\HtmlTemplates\StringEvaluator;

class StringEvaluatorTest extends BaseTest
{
    public function testBasicExpressions(): void
    {
        $object = new \stdClass();
        $object->property = 'property value';

        $variables = [
            'trueVariable' => true,
            'falseVariable' => false,
            'nullVariable' => null,
            'integerVariable' => 10,
            'stringVariable' => 'text',
            'object' => $object,
        ];

        $tests = [
            'true' => '1',
            'false' => '0',
            'null' => '',
            '$trueVariable' => '1',
            '$falseVariable' => '0',
            '$nullVariable' => '',
            '$integerVariable' => '10',
            '$stringVariable' => 'text',
            '$object.property' => 'property value',
        ];

        $evaluator = service(StringEvaluator::class);

        foreach ($tests as $string => $result) {
            self::assertEquals($result, $evaluator->evaluate($string, $variables), "source '$string'");
        }
    }

    public function testIsTruthy(): void
    {
        $tests = [
            'true' => true,
            '1' => true,
            '100' => true,
            '1.2' => true,
            '0' => false,
            'false' => false,
            'null' => false,
            '' => false,
            'string' => false,
        ];

        $evaluator = service(StringEvaluator::class);

        foreach ($tests as $string => $result) {
            self::assertEquals($result, $evaluator->isTruthy((string) $string, []), "source '$string'");
        }
    }
}
