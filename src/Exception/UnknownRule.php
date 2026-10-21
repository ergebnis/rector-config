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

namespace Ergebnis\Rector\Config\Exception;

/**
 * @no-named-arguments
 */
final class UnknownRule extends \InvalidArgumentException
{
    public static function fromClassName(string $className): self
    {
        return new self(\sprintf(
            'Rule set does not list a rule "%s", so it can not be overridden.',
            $className,
        ));
    }
}
