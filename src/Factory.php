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

use Rector\Config;
use Rector\Configuration;
use Rector\Contract;
use Rector\ValueObject;

/**
 * @no-named-arguments
 */
final class Factory
{
    /**
     * Creates a configuration builder based on a rule set.
     *
     * @throws \RuntimeException
     */
    public static function fromRuleSet(RuleSet $ruleSet): Configuration\RectorConfigBuilder
    {
        $currentPhpVersion = PhpVersion::current();

        if ($currentPhpVersion->isSmallerThan($ruleSet->phpVersion())) {
            throw new \RuntimeException(\sprintf(
                'Current PHP version "%s" is smaller than targeted PHP version "%s".',
                $currentPhpVersion->toString(),
                $ruleSet->phpVersion()->toString(),
            ));
        }

        $rules = $ruleSet->rules()->toArray();

        $rulesWithoutConfiguration = \array_keys(\array_filter($rules, static function ($configuration): bool {
            return true === $configuration;
        }));

        $rulesWithConfiguration = \array_filter($rules, static function ($configuration): bool {
            return \is_array($configuration);
        });

        /** @var ValueObject\PhpVersion::* $phpVersion */
        $phpVersion = $ruleSet->phpVersion()->toInt();

        $rectorConfigBuilder = Config\RectorConfig::configure()
            ->withPhpVersion($phpVersion)
            ->withRules($rulesWithoutConfiguration);

        /**
         * @var class-string<Contract\Rector\ConfigurableRectorInterface> $className
         */
        foreach ($rulesWithConfiguration as $className => $configuration) {
            $rectorConfigBuilder->withConfiguredRule(
                $className,
                $configuration,
            );
        }

        return $rectorConfigBuilder;
    }
}
