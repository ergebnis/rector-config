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

namespace Ergebnis\Rector\Config\Test\Unit\PhpVersion;

use Ergebnis\DataProvider;
use Ergebnis\Rector\Config\PhpVersion;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Rector\Config\PhpVersion\Major
 *
 * @no-named-arguments
 */
final class MajorTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::lessThanZero
     */
    public function testFromIntRejectsInvalidValue(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to be greater than or equal to 0, but %d is not.',
            $value,
        ));

        PhpVersion\Major::fromInt($value);
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::greaterThanZero
     * @dataProvider \Ergebnis\DataProvider\IntProvider::zero
     */
    public function testFromIntReturnsMajor(int $value): void
    {
        $major = PhpVersion\Major::fromInt($value);

        self::assertSame($value, $major->toInt());
    }
}
