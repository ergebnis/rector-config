# rector-config

[![Integrate](https://github.com/ergebnis/rector-config/actions/workflows/integrate.yaml/badge.svg?branch=main)](https://github.com/ergebnis/rector-config/actions/workflows/integrate.yaml)
[![Merge](https://github.com/ergebnis/rector-config/actions/workflows/merge.yaml/badge.svg)](https://github.com/ergebnis/rector-config/actions/workflows/merge.yaml)
[![Release](https://github.com/ergebnis/rector-config/actions/workflows/release.yaml/badge.svg)](https://github.com/ergebnis/rector-config/actions/workflows/release.yaml)
[![Renew](https://github.com/ergebnis/rector-config/actions/workflows/renew.yaml/badge.svg)](https://github.com/ergebnis/rector-config/actions/workflows/renew.yaml)

[![Code Coverage](https://codecov.io/gh/ergebnis/rector-config/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/rector-config)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/rector-config/v/stable)](https://packagist.org/packages/ergebnis/rector-config)
[![Total Downloads](https://poser.pugx.org/ergebnis/rector-config/downloads)](https://packagist.org/packages/ergebnis/rector-config)
[![Monthly Downloads](https://poser.pugx.org/ergebnis/rector-config/d/monthly)](https://packagist.org/packages/ergebnis/rector-config)

This project provides a [`composer`](https://getcomposer.org) package with a configuration factory and rule sets for [`rector/rector`](https://github.com/rectorphp/rector).

## Installation

Run

```sh
composer require --dev ergebnis/rector-config
```

## Usage

### Configuration

Pick one of the rule sets:

- [`Ergebnis\Rector\Config\RuleSet\Php53`](src/RuleSet/Php53.php)
- [`Ergebnis\Rector\Config\RuleSet\Php54`](src/RuleSet/Php54.php)
- [`Ergebnis\Rector\Config\RuleSet\Php55`](src/RuleSet/Php55.php)
- [`Ergebnis\Rector\Config\RuleSet\Php56`](src/RuleSet/Php56.php)
- [`Ergebnis\Rector\Config\RuleSet\Php70`](src/RuleSet/Php70.php)
- [`Ergebnis\Rector\Config\RuleSet\Php71`](src/RuleSet/Php71.php)
- [`Ergebnis\Rector\Config\RuleSet\Php72`](src/RuleSet/Php72.php)
- [`Ergebnis\Rector\Config\RuleSet\Php73`](src/RuleSet/Php73.php)
- [`Ergebnis\Rector\Config\RuleSet\Php74`](src/RuleSet/Php74.php)
- [`Ergebnis\Rector\Config\RuleSet\Php80`](src/RuleSet/Php80.php)

Each rule set lists every rule that `rector/rector` and `ergebnis/rector-rules` provide, except rules for frameworks and libraries, downgrade rules, and generic rules that only make sense with a project-specific configuration. A rule set enables or disables each of these rules explicitly and does not include any of the sets that `rector/rector` provides.

Create a configuration file `rector.php` in the root of your project:

```php
<?php

declare(strict_types=1);

use Ergebnis\Rector\Config;

$ruleSet = Config\RuleSet\Php53::create();

return Config\Factory::fromRuleSet($ruleSet)
    ->withCache(__DIR__ . '/.build/rector/')
    ->withPaths([
        __DIR__ . '/src/',
        __DIR__ . '/test/',
    ]);
```

`Config\Factory::fromRuleSet()` returns the `Rector\Configuration\RectorConfigBuilder` of `rector/rector`, so you can continue to configure paths, skips, and sets for frameworks and libraries, for example the sets for PHPUnit:

```diff
 <?php

 declare(strict_types=1);

 use Ergebnis\Rector\Config;
+use Rector\PHPUnit;

 $ruleSet = Config\RuleSet\Php53::create();

 return Config\Factory::fromRuleSet($ruleSet)
     ->withCache(__DIR__ . '/.build/rector/')
     ->withPaths([
         __DIR__ . '/src/',
         __DIR__ . '/test/',
-    ]);
+    ])
+    ->withSets([
+        PHPUnit\Set\PHPUnitSetList::COMPOSER_BASED,
+    ]);
```

:exclamation: Avoid sets that overlap with the rule set, for example `withPhpSets()`, `withPreparedSets()`, or `withSets()` with a set from `Rector\Set\ValueObject\SetList`. `rector/rector` then warns on every run that rules are registered twice, and the set enables rules that the rule set disables on purpose. Use `withRules()` on the rule set and `withSkip()` on the `Rector\Configuration\RectorConfigBuilder` instead.

### Git

All configuration examples use the caching feature, and if you want to use it as well, you should add the cache directory to `.gitignore`:

```diff
+ /.build/
 /vendor/
```

### Configuring a rule set that overrides rules

:bulb: Optionally override rules from a rule set by passing a set of rules:

```diff
 <?php

 declare(strict_types=1);

 use Ergebnis\Rector\Config;
+use Ergebnis\Rector\Rules;
+use Rector\CodeQuality;

-$ruleSet = Config\RuleSet\Php53::create();
+$ruleSet = Config\RuleSet\Php53::create()->withRules(Config\Rules::fromArray([
+    CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector::class => false,
+    Rules\Files\ReferenceNamespacedSymbolsRelativeToNamespacePrefixRector::class => [
+        'discoverNamespacePrefixes' => true,
+        'parentNamespacePrefixes' => [
+            'Acme\\Example',
+        ],
+    ],
+]));

 return Config\Factory::fromRuleSet($ruleSet)
     ->withCache(__DIR__ . '/.build/rector/')
     ->withPaths([
         __DIR__ . '/src/',
         __DIR__ . '/test/',
     ]);
```

Each rule passed to `withRules()` replaces the entry of the rule set as a whole:

- `false` disables the rule;
- `true` enables a rule that is not configurable;
- an array enables a configurable rule with exactly this configuration.

Passing a rule that the rule set does not list throws an `Ergebnis\Rector\Config\Exception\UnknownRule` exception.

:exclamation: Calling `withConfiguredRule()` on the `Rector\Configuration\RectorConfigBuilder` for a rule that the rule set already configures merges the configurations, so prefer `withRules()` on the rule set.

### Configuring a rule set that skips rules for paths

:bulb: Optionally skip rules for some paths with `withSkip()`:

```diff
 <?php

 declare(strict_types=1);

 use Ergebnis\Rector\Config;
+use Rector\CodeQuality;

 $ruleSet = Config\RuleSet\Php53::create();

 return Config\Factory::fromRuleSet($ruleSet)
     ->withCache(__DIR__ . '/.build/rector/')
     ->withPaths([
         __DIR__ . '/src/',
         __DIR__ . '/test/',
-    ]);
+    ])
+    ->withSkip([
+        CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector::class => [
+            __DIR__ . '/test/',
+        ],
+    ]);
```

## Changelog

The maintainers of this project record notable changes to this project in a [changelog](CHANGELOG.md).

## Contributing

The maintainers of this project suggest following the [contribution guide](.github/CONTRIBUTING.md).

## Code of Conduct

The maintainers of this project ask contributors to follow the [code of conduct](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## General Support Policy

The maintainers of this project provide limited support.

## PHP Version Support Policy

This project currently supports the following PHP versions:

- [PHP 7.4](https://www.php.net/releases/#7.4.0) (has reached its end of life on November 28, 2022)
- [PHP 8.0](https://www.php.net/releases/#8.0.0) (has reached its end of life on November 26, 2023)
- [PHP 8.1](https://www.php.net/releases/#8.1.0) (has reached its end of life on December 31, 2025)
- [PHP 8.2](https://www.php.net/releases/#8.2.0)
- [PHP 8.3](https://www.php.net/releases/#8.3.0)
- [PHP 8.4](https://www.php.net/releases/#8.4.0)
- [PHP 8.5](https://www.php.net/releases/#8.5.0)

The maintainers of this project add support for a PHP version following its initial release and _may_ drop support for a PHP version when it has reached its [end of life](https://www.php.net/supported-versions.php).

## Security Policy

This project has a [security policy](.github/SECURITY.md).

## License

This project uses the [MIT license](LICENSE.md).

## Social

Follow [@localheinz](https://x.com/intent/follow?screen_name=localheinz) and [@ergebnis](https://x.com/intent/follow?screen_name=ergebnis) on X.
