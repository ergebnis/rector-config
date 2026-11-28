# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`0.3.1...main`][0.3.1...main].

### Changed

- Disabled `Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector` in all rule sets ([#79]), by [@localheinz]

## [`0.3.1`][0.3.1]

For a full diff see [`0.3.0...0.3.1`][0.3.0...0.3.1].

### Fixed

- Required `ergebnis/rector-rules:~1.22.2` ([#78]), by [@localheinz]

## [`0.3.0`][0.3.0]

For a full diff see [`0.2.0...0.3.0`][0.2.0...0.3.0].

### Added

- Added rules for `phpunit/phpunit` to all rule sets, enabled for the version of `phpunit/phpunit` that supports the lowest PHP version of the rule set ([#77]), by [@localheinz]

## [`0.2.0`][0.2.0]

For a full diff see [`0.1.0...0.2.0`][0.1.0...0.2.0].

### Changed

- Disabled `Rector\TypeDeclaration\Rector\FuncCall\AddArrayFunctionClosureParamTypeRector` in all rule sets ([#61]), by [@localheinz]
- Disabled `Rector\TypeDeclaration\Rector\FuncCall\AddArrayAnyAllClosureParamTypeRector` in all rule sets ([#62]), by [@localheinz]
- Disabled `Rector\TypeDeclaration\Rector\FunctionLike\AddClosureParamTypeForArrayMapRector` in all rule sets ([#63]), by [@localheinz]
- Disabled `Rector\TypeDeclaration\Rector\FunctionLike\AddClosureParamTypeForArrayReduceRector` in all rule sets ([#64]), by [@localheinz]
- Disabled `Rector\TypeDeclaration\Rector\FunctionLike\AddClosureParamTypeFromIterableMethodCallRector` in all rule sets ([#65]), by [@localheinz]
- Disabled `Ergebnis\Rector\Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector` in all rule sets ([#66]), by [@localheinz]
- Required `ergebnis/rector-rules:~1.22.1` ([#75]), by [@localheinz]

## [`0.1.0`][0.1.0]

For a full diff see [`40a25bc...0.1.0`][40a25bc...0.1.0].

### Added

- Added `Factory`, `PhpVersion`, `Rules`, `RuleSet`, and `RuleSet\Php53` rule set ([#3]), by [@localheinz]
- Added `RuleSet\Php54` rule set ([#4]), by [@localheinz]
- Added `RuleSet\Php55` rule set ([#5]), by [@localheinz]
- Added `RuleSet\Php56` rule set ([#6]), by [@localheinz]
- Added `RuleSet\Php70` rule set ([#7]), by [@localheinz]
- Added `RuleSet\Php71` rule set ([#8]), by [@localheinz]
- Added `RuleSet\Php72` rule set ([#9]), by [@localheinz]
- Added `RuleSet\Php73` rule set ([#10]), by [@localheinz]
- Added `RuleSet\Php74` rule set ([#11]), by [@localheinz]
- Added `RuleSet\Php80` rule set ([#12]), by [@localheinz]
- Added `RuleSet\Php81` rule set ([#13]), by [@localheinz]
- Added `RuleSet\Php82` rule set ([#14]), by [@localheinz]
- Added `RuleSet\Php83` rule set ([#15]), by [@localheinz]
- Added `RuleSet\Php84` rule set ([#16]), by [@localheinz]
- Added `RuleSet\Php85` rule set ([#17]), by [@localheinz]
- Added support for PHP 8.6 ([#46]), by [@localheinz]
- Added `RuleSet\Php86` rule set ([#55]), by [@localheinz]

### Changed

- Allowed installation on PHP 8.7 ([#52]), by [@localheinz]
- Required `rector/rector:^2.9.2` ([#57]), by [@localheinz]

[0.1.0]: https://github.com/ergebnis/rector-config/releases/tag/0.1.0
[0.1.0...0.2.0]: https://github.com/ergebnis/rector-config/compare/0.1.0...0.2.0
[0.2.0]: https://github.com/ergebnis/rector-config/releases/tag/0.2.0
[0.2.0...0.3.0]: https://github.com/ergebnis/rector-config/compare/0.2.0...0.3.0
[0.3.0]: https://github.com/ergebnis/rector-config/releases/tag/0.3.0
[0.3.0...0.3.1]: https://github.com/ergebnis/rector-config/compare/0.3.0...0.3.1
[0.3.1]: https://github.com/ergebnis/rector-config/releases/tag/0.3.1
[0.3.1...main]: https://github.com/ergebnis/rector-config/compare/0.3.1...main
[40a25bc...0.1.0]: https://github.com/ergebnis/rector-config/compare/40a25bc...0.1.0

[#3]: https://github.com/ergebnis/rector-config/pull/3
[#4]: https://github.com/ergebnis/rector-config/pull/4
[#5]: https://github.com/ergebnis/rector-config/pull/5
[#6]: https://github.com/ergebnis/rector-config/pull/6
[#7]: https://github.com/ergebnis/rector-config/pull/7
[#8]: https://github.com/ergebnis/rector-config/pull/8
[#9]: https://github.com/ergebnis/rector-config/pull/9
[#10]: https://github.com/ergebnis/rector-config/pull/10
[#11]: https://github.com/ergebnis/rector-config/pull/11
[#12]: https://github.com/ergebnis/rector-config/pull/12
[#13]: https://github.com/ergebnis/rector-config/pull/13
[#14]: https://github.com/ergebnis/rector-config/pull/14
[#15]: https://github.com/ergebnis/rector-config/pull/15
[#16]: https://github.com/ergebnis/rector-config/pull/16
[#17]: https://github.com/ergebnis/rector-config/pull/17
[#46]: https://github.com/ergebnis/rector-config/pull/46
[#52]: https://github.com/ergebnis/rector-config/pull/52
[#55]: https://github.com/ergebnis/rector-config/pull/55
[#57]: https://github.com/ergebnis/rector-config/pull/57
[#61]: https://github.com/ergebnis/rector-config/pull/61
[#62]: https://github.com/ergebnis/rector-config/pull/62
[#63]: https://github.com/ergebnis/rector-config/pull/63
[#64]: https://github.com/ergebnis/rector-config/pull/64
[#65]: https://github.com/ergebnis/rector-config/pull/65
[#66]: https://github.com/ergebnis/rector-config/pull/66
[#75]: https://github.com/ergebnis/rector-config/pull/75
[#77]: https://github.com/ergebnis/rector-config/pull/77
[#78]: https://github.com/ergebnis/rector-config/pull/78
[#79]: https://github.com/ergebnis/rector-config/pull/79

[@localheinz]: https://github.com/localheinz
