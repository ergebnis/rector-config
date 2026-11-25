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

namespace Ergebnis\Rector\Config\Test\Unit;

use Ergebnis\Rector\Config;
use Ergebnis\Rector\Rules;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Rector\Config\RuleSet
 *
 * @uses \Ergebnis\Rector\Config\Exception\InvalidRuleConfiguration
 * @uses \Ergebnis\Rector\Config\PhpVersion
 * @uses \Ergebnis\Rector\Config\PhpVersion\Major
 * @uses \Ergebnis\Rector\Config\PhpVersion\Minor
 * @uses \Ergebnis\Rector\Config\PhpVersion\Patch
 * @uses \Ergebnis\Rector\Config\Rules
 */
final class RuleSetTest extends Framework\TestCase
{
    use Config\Test\Util\Helper;

    public function testCreateReturnsRuleSet(): void
    {
        $phpVersion = self::phpVersion();

        $rules = Config\Rules::fromArray([
            Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector::class => false,
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => true,
        ]);

        $ruleSet = Config\RuleSet::create(
            $phpVersion,
            $rules,
        );

        self::assertSame($phpVersion, $ruleSet->phpVersion());
        self::assertSame($rules, $ruleSet->rules());
    }

    public function testWithRulesThrowsInvalidRuleConfigurationWhenClassIsNotRectorRule(): void
    {
        $ruleSet = Config\RuleSet::create(
            self::phpVersion(),
            Config\Rules::fromArray([
                Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => true,
            ]),
        );

        $this->expectException(Config\Exception\InvalidRuleConfiguration::class);

        $ruleSet->withRules(Config\Rules::fromArray([
            self::class => true,
        ]));
    }

    public function testWithRulesThrowsInvalidRuleConfigurationWhenConfigurationIsNotBoolOrArray(): void
    {
        $ruleSet = Config\RuleSet::create(
            self::phpVersion(),
            Config\Rules::fromArray([
                Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => true,
            ]),
        );

        $this->expectException(Config\Exception\InvalidRuleConfiguration::class);

        $ruleSet->withRules(Config\Rules::fromArray([
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => 'yes',
        ]));
    }

    public function testWithRulesThrowsInvalidRuleConfigurationWhenConfigurationIsTrueForRuleThatIsConfigurable(): void
    {
        $ruleSet = Config\RuleSet::create(
            self::phpVersion(),
            Config\Rules::fromArray([
                Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => false,
            ]),
        );

        $this->expectException(Config\Exception\InvalidRuleConfiguration::class);

        $ruleSet->withRules(Config\Rules::fromArray([
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => true,
        ]));
    }

    public function testWithRulesThrowsInvalidRuleConfigurationWhenConfigurationIsArrayForRuleThatIsNotConfigurable(): void
    {
        $ruleSet = Config\RuleSet::create(
            self::phpVersion(),
            Config\Rules::fromArray([
                Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => false,
            ]),
        );

        $this->expectException(Config\Exception\InvalidRuleConfiguration::class);

        $ruleSet->withRules(Config\Rules::fromArray([
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => [
                'foo' => 'bar',
            ],
        ]));
    }

    public function testWithRulesReturnsRuleSetWithAddedRulesWhenRulesAreNotListed(): void
    {
        $rules = Config\Rules::fromArray([
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => [
                'direction' => 'desc',
            ],
        ]);

        $ruleSet = Config\RuleSet::create(
            self::phpVersion(),
            $rules,
        );

        $mutated = $ruleSet->withRules(Config\Rules::fromArray([
            Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector::class => true,
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => false,
        ]));

        self::assertNotSame($ruleSet, $mutated);
        self::assertSame($rules, $ruleSet->rules());
        self::assertSame($ruleSet->phpVersion(), $mutated->phpVersion());

        $expected = [
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => [
                'direction' => 'desc',
            ],
            Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector::class => true,
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => false,
        ];

        self::assertSame($expected, $mutated->rules()->toArray());
    }

    public function testWithRulesReturnsRuleSetWithReplacedRulesWhenRulesAreListed(): void
    {
        $rules = Config\Rules::fromArray([
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => [
                'comparison_function' => 'strcmp',
                'direction' => 'asc',
            ],
            Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector::class => true,
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => false,
        ]);

        $ruleSet = Config\RuleSet::create(
            self::phpVersion(),
            $rules,
        );

        $mutated = $ruleSet->withRules(Config\Rules::fromArray([
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => [
                'direction' => 'desc',
            ],
            Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector::class => false,
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => true,
        ]));

        self::assertNotSame($ruleSet, $mutated);
        self::assertSame($rules, $ruleSet->rules());
        self::assertSame($ruleSet->phpVersion(), $mutated->phpVersion());

        $expected = [
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => [
                'direction' => 'desc',
            ],
            Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector::class => false,
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => true,
        ];

        self::assertSame($expected, $mutated->rules()->toArray());
    }

    private static function phpVersion(): Config\PhpVersion
    {
        $faker = self::faker();

        return Config\PhpVersion::create(
            Config\PhpVersion\Major::fromInt($faker->numberBetween(5, 8)),
            Config\PhpVersion\Minor::fromInt($faker->numberBetween(0, 4)),
            Config\PhpVersion\Patch::fromInt($faker->numberBetween(0, 99)),
        );
    }
}
