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

use Ergebnis\Rector;
use Rector\PHPUnit;

$ruleSet = Rector\Config\RuleSet\Php74::create()->withRules(Rector\Config\Rules::fromArray([
    Rector\Rules\Files\ReferenceNamespacedSymbolsRelativeToNamespacePrefixRector::class => [
        'discoverNamespacePrefixes' => true,
        'parentNamespacePrefixes' => [
            'Ergebnis\Rector\Config',
        ],
    ],
]));

return Rector\Config\Factory::fromRuleSet($ruleSet)
    ->withCache(__DIR__ . '/.build/rector/')
    ->withPaths([
        __DIR__ . '/src/',
        __DIR__ . '/test/',
    ])
    ->withSets([
        PHPUnit\Set\PHPUnitSetList::COMPOSER_BASED,
    ])
    ->withSkip([
        Rector\Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => [
            __DIR__ . '/src/RuleSet/',
            __DIR__ . '/test/Unit/RuleSet/',
        ],
    ]);
