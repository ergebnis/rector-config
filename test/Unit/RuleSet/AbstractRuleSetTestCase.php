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

use Ergebnis\Rector\Config\PhpVersion;
use Ergebnis\Rector\Config\Rules;
use Ergebnis\Rector\Config\RuleSet;
use Ergebnis\Rector\Config\Test;
use PHPUnit\Framework;

abstract class AbstractRuleSetTestCase extends Framework\TestCase
{
    final public function testDefaults(): void
    {
        $ruleSet = static::createRuleSet();

        self::assertEquals($this->expectedPhpVersion(), $ruleSet->phpVersion());
        self::assertEquals($this->expectedRules(), $ruleSet->rules());
    }

    final public function testRuleSetListsAllRulesInScope(): void
    {
        $rulesThatAreNotListed = \array_values(\array_diff(
            Test\Util\RulesInScope::all(),
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
            Test\Util\RulesInScope::all(),
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

            if (Test\Util\RulesInScope::isConfigurable($rule) === \is_array($configuration)) {
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

            if (!Test\Util\RulesInScope::hasMinimumPhpVersion($rule)) {
                continue;
            }

            if (Test\Util\RulesInScope::minimumPhpVersion($rule) <= $phpVersion) {
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

    abstract protected static function createRuleSet(): RuleSet;

    abstract protected function expectedPhpVersion(): PhpVersion;

    abstract protected function expectedRules(): Rules;
}
