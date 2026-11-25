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

namespace Ergebnis\Rector\Config\Test\Unit\Exception;

use Ergebnis\Rector\Config;
use Ergebnis\Rector\Rules;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Rector\Config\Exception\InvalidRuleConfiguration
 */
final class InvalidRuleConfigurationTest extends Framework\TestCase
{
    public function testNotRectorRuleReturnsInvalidRuleConfiguration(): void
    {
        $className = self::class;

        $exception = Config\Exception\InvalidRuleConfiguration::notRectorRule($className);

        $message = \sprintf(
            'Class "%s" does not exist or is not a rule for rector/rector.',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testArrayForRuleThatIsNotConfigurableReturnsInvalidRuleConfiguration(): void
    {
        $className = Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class;

        $exception = Config\Exception\InvalidRuleConfiguration::arrayForRuleThatIsNotConfigurable($className);

        $message = \sprintf(
            'Rule "%s" is not configurable, so it can only be enabled with true or disabled with false, not configured with an array.',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testTrueForRuleThatIsConfigurableReturnsInvalidRuleConfiguration(): void
    {
        $className = Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class;

        $exception = Config\Exception\InvalidRuleConfiguration::trueForRuleThatIsConfigurable($className);

        $message = \sprintf(
            'Rule "%s" is configurable, so it can only be enabled with an array or disabled with false, not enabled with true.',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testNotBoolOrArrayReturnsInvalidRuleConfiguration(): void
    {
        $className = Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class;

        $exception = Config\Exception\InvalidRuleConfiguration::notBoolOrArray($className);

        $message = \sprintf(
            'Rule "%s" can only be enabled with true or an array, or disabled with false.',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
