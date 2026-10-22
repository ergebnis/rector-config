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

namespace Ergebnis\Rector\Config\Test\EndToEnd\RuleSet;

use Ergebnis\Rector\Config\RuleSet;

/**
 * @coversNothing
 */
final class Php70Test extends AbstractRuleSetTestCase
{
    protected static function createRuleSet(): RuleSet
    {
        return RuleSet\Php70::create();
    }
}
