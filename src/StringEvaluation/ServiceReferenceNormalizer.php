<?php

declare(strict_types=1);

namespace Medas\HtmlTemplates\StringEvaluation;

use Medas\HtmlTemplates\Exceptions\MultipleServicesWithShortNameException;
use Medas\HtmlTemplates\Exceptions\NoServiceWithShortNameException;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ServiceReferenceNormalizer
{
    public function normalize(string $expression): string
    {
        if (!preg_match_all('/\b(\w+)::/', $expression, $matches, PREG_SET_ORDER)) {
            return $expression;
        }

        foreach ($matches as $match) {
            [$whole, $name] = $match;

            if (!class_exists($name)) {
                $name = $this->findServiceByShortName($name);
            }

            $expression = str_replace($whole, '\service(' . $name . '::class)->', $expression);
        }

        return $expression;
    }

    private function findServiceByShortName(mixed $shortName): string
    {
        $services = [];

        foreach (sm()->getServiceClassNames() as $className) {
            if (str_ends_with($className, '\\' . $shortName)) {
                $services[] = $className;
            }
        }

        if (count($services) === 0) {
            throw new NoServiceWithShortNameException($shortName);
        }

        if (count($services) >= 2) {
            throw new MultipleServicesWithShortNameException($shortName, $services);
        }

        return '\\' . $services[0];
    }
}
