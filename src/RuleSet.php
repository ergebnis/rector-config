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

use Rector\Contract;

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
     * Returns a rule set in which each of the given rules replaces the listed rule as a whole, or is added when the rule set does not list it.
     *
     * @throws Exception\InvalidRuleConfiguration
     */
    public function withRules(Rules $rules): self
    {
        $value = $this->rules->toArray();

        foreach ($rules->toArray() as $className => $configuration) {
            if (!\is_a($className, Contract\Rector\RectorInterface::class, true)) {
                throw Exception\InvalidRuleConfiguration::notRectorRule($className);
            }

            if (
                !\is_bool($configuration)
                && !\is_array($configuration)
            ) {
                throw Exception\InvalidRuleConfiguration::notBoolOrArray($className);
            }

            $isConfigurable = \is_a(
                $className,
                Contract\Rector\ConfigurableRectorInterface::class,
                true,
            );

            if (
                true === $configuration
                && $isConfigurable
            ) {
                throw Exception\InvalidRuleConfiguration::trueForRuleThatIsConfigurable($className);
            }

            if (
                \is_array($configuration)
                && !$isConfigurable
            ) {
                throw Exception\InvalidRuleConfiguration::arrayForRuleThatIsNotConfigurable($className);
            }

            $value[$className] = $configuration;
        }

        return new self(
            $this->phpVersion,
            Rules::fromArray($value),
        );
    }
}
