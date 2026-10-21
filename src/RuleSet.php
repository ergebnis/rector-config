<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/rector-config
 */

namespace Ergebnis\Rector\Config;

/**
 * @no-named-arguments
 */
final class RuleSet
{
    private PhpVersion $phpVersion;
    private Rules $rules;

    private function __construct(
        PhpVersion $phpVersion,
        Rules $rules
    ) {
        $this->phpVersion = $phpVersion;
        $this->rules = $rules;
    }

    public static function create(
        PhpVersion $phpVersion,
        Rules $rules
    ): self {
        return new self(
            $phpVersion,
            $rules,
        );
    }

    public function phpVersion(): PhpVersion
    {
        return $this->phpVersion;
    }

    public function rules(): Rules
    {
        return $this->rules;
    }

    /**
     * Returns a rule set in which each of the given rules replaces the listed rule as a whole.
     *
     * @throws Exception\UnknownRule
     */
    public function withRules(Rules $rules): self
    {
        $value = $this->rules->toArray();

        foreach ($rules->toArray() as $className => $configuration) {
            if (!\array_key_exists($className, $value)) {
                throw Exception\UnknownRule::fromClassName($className);
            }

            $value[$className] = $configuration;
        }

        return new self(
            $this->phpVersion,
            Rules::fromArray($value),
        );
    }
}
