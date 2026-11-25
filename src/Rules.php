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

namespace Ergebnis\Rector\Config;

use Rector\Contract;

/**
 * @no-named-arguments
 */
final class Rules
{
    /**
     * @var array<class-string<Contract\Rector\RectorInterface>, array<array-key, mixed>|bool>
     */
    private array $value;

    /**
     * @param array<class-string<Contract\Rector\RectorInterface>, array<array-key, mixed>|bool> $value
     */
    private function __construct(array $value)
    {
        $this->value = $value;
    }

    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @param array<class-string<Contract\Rector\RectorInterface>, array<array-key, mixed>|bool> $value
     */
    public static function fromArray(array $value): self
    {
        return new self($value);
    }

    /**
     * @return array<class-string<Contract\Rector\RectorInterface>, array<array-key, mixed>|bool>
     */
    public function toArray(): array
    {
        return $this->value;
    }
}
