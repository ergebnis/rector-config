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
use Rector\Configuration;

/**
 * @covers \Ergebnis\Rector\Config\Factory
 *
 * @uses \Ergebnis\Rector\Config\PhpVersion
 * @uses \Ergebnis\Rector\Config\PhpVersion\Major
 * @uses \Ergebnis\Rector\Config\PhpVersion\Minor
 * @uses \Ergebnis\Rector\Config\PhpVersion\Patch
 * @uses \Ergebnis\Rector\Config\Rules
 * @uses \Ergebnis\Rector\Config\RuleSet
 */
final class FactoryTest extends Framework\TestCase
{
    protected function setUp(): void
    {
        Configuration\Parameter\SimpleParameterProvider::setParameter(Configuration\Option::REGISTERED_RECTOR_RULES, []);
        Configuration\Parameter\SimpleParameterProvider::setParameter(Configuration\Option::RULE_CONFIGURATIONS, []);
    }

    public function testFromRuleSetThrowsRuntimeExceptionWhenCurrentPhpVersionIsLessThanTargetPhpVersion(): void
    {
        $phpVersion = Config\PhpVersion::create(
            Config\PhpVersion\Major::fromInt(\PHP_MAJOR_VERSION + 1),
            Config\PhpVersion\Minor::fromInt(0),
            Config\PhpVersion\Patch::fromInt(0),
        );

        $ruleSet = Config\RuleSet::create(
            $phpVersion,
            Config\Rules::empty(),
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Current PHP version "%s" is less than targeted PHP version "%s".',
            Config\PhpVersion::current()->toString(),
            $phpVersion->toString(),
        ));

        Config\Factory::fromRuleSet($ruleSet);
    }

    public function testFromRuleSetReturnsRectorConfigBuilderWhenCurrentPhpVersionIsEqualToOrGreaterThanTargetPhpVersion(): void
    {
        $phpVersion = Config\PhpVersion::current();

        $configuration = [
            'comparison_function' => 'strcmp',
            'direction' => 'desc',
        ];

        $ruleSet = Config\RuleSet::create(
            $phpVersion,
            Config\Rules::fromArray([
                Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => $configuration,
                Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector::class => false,
                Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => true,
            ]),
        );

        $rectorConfigBuilder = Config\Factory::fromRuleSet($ruleSet);

        $rectorConfigBuilder(new \Rector\Config\RectorConfig());

        $expectedRegisteredRules = [
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class,
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class,
        ];

        self::assertEqualsCanonicalizing($expectedRegisteredRules, Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::REGISTERED_RECTOR_RULES));

        $expectedRuleConfigurations = [
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => $configuration,
        ];

        self::assertSame($expectedRuleConfigurations, Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::RULE_CONFIGURATIONS));
        self::assertSame($phpVersion->toInt(), Configuration\Parameter\SimpleParameterProvider::provideIntParameter(Configuration\Option::PHP_VERSION_FEATURES));
    }

    public function testFromRuleSetReturnsRectorConfigBuilderWhenRuleSetEnablesOnlyRulesWithoutConfiguration(): void
    {
        $ruleSet = Config\RuleSet::create(
            Config\PhpVersion::current(),
            Config\Rules::fromArray([
                Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => true,
            ]),
        );

        $rectorConfigBuilder = Config\Factory::fromRuleSet($ruleSet);

        $rectorConfigBuilder(new \Rector\Config\RectorConfig());

        $expectedRegisteredRules = [
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class,
        ];

        self::assertSame($expectedRegisteredRules, Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::REGISTERED_RECTOR_RULES));
        self::assertSame([], Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::RULE_CONFIGURATIONS));
    }

    public function testFromRuleSetReturnsRectorConfigBuilderWhenRuleSetEnablesOnlyRulesWithConfiguration(): void
    {
        $configuration = [
            'comparison_function' => 'strcmp',
            'direction' => 'asc',
        ];

        $ruleSet = Config\RuleSet::create(
            Config\PhpVersion::current(),
            Config\Rules::fromArray([
                Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => $configuration,
            ]),
        );

        $rectorConfigBuilder = Config\Factory::fromRuleSet($ruleSet);

        $rectorConfigBuilder(new \Rector\Config\RectorConfig());

        $expectedRegisteredRules = [
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class,
        ];

        self::assertSame($expectedRegisteredRules, Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::REGISTERED_RECTOR_RULES));

        $expectedRuleConfigurations = [
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => $configuration,
        ];

        self::assertSame($expectedRuleConfigurations, Configuration\Parameter\SimpleParameterProvider::provideArrayParameter(Configuration\Option::RULE_CONFIGURATIONS));
    }
}
