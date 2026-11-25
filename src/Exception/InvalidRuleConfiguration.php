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
final class InvalidRuleConfiguration extends \InvalidArgumentException
{
    public static function notRectorRule(string $className): self
    {
        return new self(\sprintf(
            'Class "%s" does not exist or is not a rule for rector/rector.',
            $className,
        ));
    }

    public static function arrayForRuleThatIsNotConfigurable(string $className): self
    {
        return new self(\sprintf(
            'Rule "%s" is not configurable, so it can only be enabled with true or disabled with false, not configured with an array.',
            $className,
        ));
    }

    public static function trueForRuleThatIsConfigurable(string $className): self
    {
        return new self(\sprintf(
            'Rule "%s" is configurable, so it can only be enabled with an array or disabled with false, not enabled with true.',
            $className,
        ));
    }

    public static function notBoolOrArray(string $className): self
    {
        return new self(\sprintf(
            'Rule "%s" can only be enabled with true or an array, or disabled with false.',
            $className,
        ));
    }
}
