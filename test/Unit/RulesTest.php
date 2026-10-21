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
 * @covers \Ergebnis\Rector\Config\Rules
 */
final class RulesTest extends Framework\TestCase
{
    public function testEmptyReturnsRules(): void
    {
        $rules = Config\Rules::empty();

        self::assertSame([], $rules->toArray());
    }

    public function testFromArrayReturnsRules(): void
    {
        $value = [
            Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => [
                'comparison_function' => 'strcmp',
                'direction' => 'asc',
            ],
            Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector::class => false,
            Rules\Faker\GeneratorPropertyFetchToMethodCallRector::class => true,
        ];

        $rules = Config\Rules::fromArray($value);

        self::assertSame($value, $rules->toArray());
    }
}
