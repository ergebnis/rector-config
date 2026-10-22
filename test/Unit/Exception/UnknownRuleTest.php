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

use Ergebnis\Rector\Config\Exception;
use Ergebnis\Rector\Rules;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Rector\Config\Exception\UnknownRule
 */
final class UnknownRuleTest extends Framework\TestCase
{
    public function testFromClassNameReturnsUnknownRule(): void
    {
        $className = Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class;

        $exception = Exception\UnknownRule::fromClassName($className);

        $message = \sprintf(
            'Rule set does not list a rule "%s", so it can not be overridden.',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
