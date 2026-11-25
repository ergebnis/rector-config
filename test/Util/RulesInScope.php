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

namespace Ergebnis\Rector\Config\Test\Util;

use Rector\Configuration;
use Rector\Contract;
use Rector\VersionBonding;

/**
 * Lists the rules that every rule set has to decide.
 */
final class RulesInScope
{
    /**
     * @var list<string>
     */
    private const EXCLUDED_NAMESPACE_PREFIXES = [
        'Ergebnis\\Rector\\Rules\\PHPUnit\\',
        'Rector\\Arguments\\',
        'Rector\\Carbon\\',
        'Rector\\Doctrine\\',
        'Rector\\DowngradePhp',
        'Rector\\PHPUnit\\',
        'Rector\\Removing\\',
        'Rector\\Renaming\\',
        'Rector\\Symfony\\',
        'Rector\\Transform\\',
    ];

    /**
     * @return list<class-string<Contract\Rector\RectorInterface>>
     */
    public static function all(): array
    {
        /** @var null|list<class-string<Contract\Rector\RectorInterface>> $rules */
        static $rules;

        if (null === $rules) {
            $rules = [];

            $classNames = \array_merge(
                self::classNamesFromRector(),
                self::classNamesFromErgebnisRectorRules(),
            );

            foreach ($classNames as $className) {
                if (!self::isInScope($className)) {
                    continue;
                }

                if (!\is_a($className, Contract\Rector\RectorInterface::class, true)) {
                    continue;
                }

                $rules[] = $className;
            }

            \sort($rules);
        }

        return $rules;
    }

    /**
     * @param class-string<Contract\Rector\RectorInterface> $className
     */
    public static function isConfigurable(string $className): bool
    {
        return \is_a(
            $className,
            Contract\Rector\ConfigurableRectorInterface::class,
            true,
        );
    }

    /**
     * @param class-string<Contract\Rector\RectorInterface> $className
     */
    public static function hasMinimumPhpVersion(string $className): bool
    {
        return \is_a(
            $className,
            VersionBonding\Contract\MinPhpVersionInterface::class,
            true,
        );
    }

    /**
     * @param class-string<Contract\Rector\RectorInterface> $className
     *
     * @throws \InvalidArgumentException
     */
    public static function minimumPhpVersion(string $className): int
    {
        $rector = (new \ReflectionClass($className))->newInstanceWithoutConstructor();

        if (!$rector instanceof VersionBonding\Contract\MinPhpVersionInterface) {
            throw new \InvalidArgumentException(\sprintf(
                'Rule "%s" does not declare a minimum PHP version.',
                $className,
            ));
        }

        return $rector->provideMinPhpVersion();
    }

    /**
     * @return list<string>
     */
    private static function classNamesFromRector(): array
    {
        /** @var array<string, string> $classMap */
        $classMap = require __DIR__ . '/../../vendor/rector/rector/vendor/composer/autoload_classmap.php';

        return \array_values(\array_filter(\array_keys($classMap), static function (string $className): bool {
            return 0 === \mb_strpos($className, 'Rector\\')
                && 'Rector' === \mb_substr($className, -6);
        }));
    }

    /**
     * @return list<string>
     */
    private static function classNamesFromErgebnisRectorRules(): array
    {
        $directory = __DIR__ . '/../../vendor/ergebnis/rector-rules/src';

        $classNames = [];

        /** @var \SplFileInfo $fileInfo */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)) as $fileInfo) {
            if ('php' !== $fileInfo->getExtension()) {
                continue;
            }

            $relativePath = \mb_substr(
                $fileInfo->getPathname(),
                \mb_strlen($directory) + 1,
                -4,
            );

            $classNames[] = 'Ergebnis\\Rector\\Rules\\' . \str_replace(
                '/',
                '\\',
                $relativePath,
            );
        }

        return $classNames;
    }

    private static function isInScope(string $className): bool
    {
        foreach (self::EXCLUDED_NAMESPACE_PREFIXES as $excludedNamespacePrefix) {
            if (0 === \mb_strpos($className, $excludedNamespacePrefix)) {
                return false;
            }
        }

        if (!\class_exists($className)) {
            return false;
        }

        $reflectionClass = new \ReflectionClass($className);

        if ($reflectionClass->isAbstract()) {
            return false;
        }

        if (!$reflectionClass->implementsInterface(Contract\Rector\RectorInterface::class)) {
            return false;
        }

        if ($reflectionClass->implementsInterface(Configuration\Deprecation\Contract\DeprecatedInterface::class)) {
            return false;
        }

        $docComment = $reflectionClass->getDocComment();

        return !\is_string($docComment) || false === \mb_strpos($docComment, '@deprecated');
    }
}
