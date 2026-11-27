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

namespace Ergebnis\Rector\Config\Test\Unit\RuleSet;

use Ergebnis\Rector\Config;
use PHPUnit\Framework;

abstract class AbstractRuleSetTestCase extends Framework\TestCase
{
    /**
     * The version of phpunit/phpunit that supports the PHP version of a rule set, keyed by that PHP version.
     *
     * @var array<int, string>
     */
    private const PHPUNIT_VERSIONS = [
        50300 => '4.8',
        50400 => '4.8',
        50500 => '4.8',
        50600 => '5.7',
        70000 => '6.5',
        70100 => '7.5',
        70200 => '8.5',
        70300 => '9.6',
        70400 => '9.6',
        80000 => '9.6',
        80100 => '10.5',
        80200 => '11.5',
        80300 => '12.5',
        80400 => '13.0',
        80500 => '13.0',
        80600 => '13.0',
    ];

    final public function testDefaults(): void
    {
        $ruleSet = static::createRuleSet();

        self::assertEquals($this->expectedPhpVersion(), $ruleSet->phpVersion());
        self::assertEquals($this->expectedRules(), $ruleSet->rules());
    }

    final public function testRuleSetListsAllRulesInScope(): void
    {
        $rulesThatAreNotListed = \array_values(\array_diff(
            Config\Test\Util\RulesInScope::all(),
            \array_keys(static::createRuleSet()->rules()->toArray()),
        ));

        self::assertSame([], $rulesThatAreNotListed, \sprintf(
            "Failed asserting that rule set \"%s\" lists all rules in scope. Decide the following rules:\n\n%s",
            static::class,
            \implode("\n", $rulesThatAreNotListed),
        ));
    }

    final public function testRuleSetDoesNotListRulesThatAreNotInScope(): void
    {
        $rulesThatAreNotInScope = \array_values(\array_diff(
            \array_keys(static::createRuleSet()->rules()->toArray()),
            Config\Test\Util\RulesInScope::all(),
        ));

        self::assertSame([], $rulesThatAreNotInScope, \sprintf(
            "Failed asserting that rule set \"%s\" does not list rules that do not exist, are deprecated, or are out of scope:\n\n%s",
            static::class,
            \implode("\n", $rulesThatAreNotInScope),
        ));
    }

    final public function testRuleSetConfiguresEnabledRulesThatAreConfigurableAndOnlyThose(): void
    {
        $rulesWithWrongConfiguration = [];

        foreach (static::createRuleSet()->rules()->toArray() as $rule => $configuration) {
            if (false === $configuration) {
                continue;
            }

            if (Config\Test\Util\RulesInScope::isConfigurable($rule) === \is_array($configuration)) {
                continue;
            }

            $rulesWithWrongConfiguration[] = $rule;
        }

        self::assertSame([], $rulesWithWrongConfiguration, \sprintf(
            "Failed asserting that rule set \"%s\" configures enabled rules that are configurable, and only those:\n\n%s",
            static::class,
            \implode("\n", $rulesWithWrongConfiguration),
        ));
    }

    final public function testRuleSetDoesNotEnableRulesWithMinimumPhpVersionGreaterThanPhpVersionOfRuleSet(): void
    {
        $ruleSet = static::createRuleSet();

        $phpVersion = $ruleSet->phpVersion()->toInt();

        $rulesWithGreaterMinimumPhpVersion = [];

        foreach ($ruleSet->rules()->toArray() as $rule => $configuration) {
            if (false === $configuration) {
                continue;
            }

            if (!Config\Test\Util\RulesInScope::hasMinimumPhpVersion($rule)) {
                continue;
            }

            if (Config\Test\Util\RulesInScope::minimumPhpVersion($rule) <= $phpVersion) {
                continue;
            }

            $rulesWithGreaterMinimumPhpVersion[] = $rule;
        }

        self::assertSame([], $rulesWithGreaterMinimumPhpVersion, \sprintf(
            "Failed asserting that rule set \"%s\" does not enable rules with a minimum PHP version greater than its own:\n\n%s",
            static::class,
            \implode("\n", $rulesWithGreaterMinimumPhpVersion),
        ));
    }

    final public function testRuleSetDoesNotEnableRulesForVersionsOfPhpUnitGreaterThanPhpUnitVersionOfRuleSet(): void
    {
        $ruleSet = static::createRuleSet();

        $phpUnitVersion = self::PHPUNIT_VERSIONS[$ruleSet->phpVersion()->toInt()];

        $rulesForGreaterPhpUnitVersion = [];

        foreach ($ruleSet->rules()->toArray() as $rule => $configuration) {
            if (false === $configuration) {
                continue;
            }

            if (!Config\Test\Util\RulesInScope::hasMinimumPhpUnitVersion($rule)) {
                continue;
            }

            if (\version_compare(Config\Test\Util\RulesInScope::minimumPhpUnitVersion($rule), $phpUnitVersion, '<=')) {
                continue;
            }

            $rulesForGreaterPhpUnitVersion[] = $rule;
        }

        self::assertSame([], $rulesForGreaterPhpUnitVersion, \sprintf(
            "Failed asserting that rule set \"%s\" does not enable rules for a version of phpunit/phpunit greater than %s:\n\n%s",
            static::class,
            $phpUnitVersion,
            \implode("\n", $rulesForGreaterPhpUnitVersion),
        ));
    }

    final public function testRulesAreSortedByNameInRuleSet(): void
    {
        $rules = \array_keys(static::createRuleSet()->rules()->toArray());

        $sorted = $rules;

        \sort($sorted);

        self::assertSame($sorted, $rules, \sprintf(
            'Failed asserting that rules are sorted by name in rule set "%s".',
            static::class,
        ));
    }

    abstract protected static function createRuleSet(): Config\RuleSet;

    abstract protected function expectedPhpVersion(): Config\PhpVersion;

    abstract protected function expectedRules(): Config\Rules;
}
