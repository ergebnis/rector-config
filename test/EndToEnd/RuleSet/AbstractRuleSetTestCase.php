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

use Ergebnis\Rector\Config\PhpVersion;
use Ergebnis\Rector\Config\RuleSet;
use PHPUnit\Framework;
use Symfony\Component;

abstract class AbstractRuleSetTestCase extends Framework\TestCase
{
    final protected function setUp(): void
    {
        self::fileSystem()->mkdir(self::temporaryDirectory());

        self::fileSystem()->dumpFile(
            self::configPath(),
            self::configContents(),
        );

        self::fileSystem()->dumpFile(
            self::examplePath(),
            self::exampleContents(),
        );
    }

    final protected function tearDown(): void
    {
        self::fileSystem()->remove(self::temporaryDirectory());
    }

    final public function testRectorRunsWithConfigurationUsingRuleSet(): void
    {
        $ruleSet = static::createRuleSet();

        if (PhpVersion::current()->isSmallerThan($ruleSet->phpVersion())) {
            self::markTestSkipped(\sprintf(
                'Rule set "%s" requires at least PHP %s.',
                self::ruleSetClassName(),
                $ruleSet->phpVersion()->toString(),
            ));
        }

        $process = new Component\Process\Process([
            \PHP_BINARY,
            '-d',
            'display_errors=stderr',
            'vendor/bin/rector',
            'process',
            \sprintf(
                '--config=%s',
                self::configPath(),
            ),
            '--dry-run',
            '--no-ansi',
            '--output-format=json',
        ]);

        $process->run();

        $output = $process->getOutput();

        /** @var mixed $result */
        $result = \json_decode(
            $output,
            true,
        );

        self::assertIsArray($result, \sprintf(
            "Failed asserting that running rector/rector with a configuration using rule set \"%s\" produced a JSON report. Output:\n\n%s\n%s",
            static::class,
            $output,
            $process->getErrorOutput(),
        ));

        self::assertArrayHasKey('totals', $result);

        $totals = $result['totals'];

        self::assertIsArray($totals);
        self::assertArrayHasKey('errors', $totals);
        self::assertSame(0, $totals['errors'], \sprintf(
            "Failed asserting that running rector/rector with a configuration using rule set \"%s\" did not result in errors. Output:\n\n%s",
            static::class,
            $output,
        ));
    }

    abstract protected static function createRuleSet(): RuleSet;

    /**
     * Derives the rule set from the name of the test, for example RuleSet\Php74 from Php74Test.
     */
    private static function ruleSetClassName(): string
    {
        $parts = \explode(
            '\\',
            static::class,
        );

        return \sprintf(
            'Ergebnis\\Rector\\Config\\RuleSet\\%s',
            \mb_substr(
                \end($parts),
                0,
                -4,
            ),
        );
    }

    private static function fileSystem(): Component\Filesystem\Filesystem
    {
        return new Component\Filesystem\Filesystem();
    }

    private static function temporaryDirectory(): string
    {
        return \sprintf(
            '%s/ergebnis-rector-config/%s',
            \sys_get_temp_dir(),
            \str_replace(
                '\\',
                '-',
                static::class,
            ),
        );
    }

    private static function configPath(): string
    {
        return \sprintf(
            '%s/rector.php',
            self::temporaryDirectory(),
        );
    }

    private static function examplePath(): string
    {
        return \sprintf(
            '%s/Example.php',
            self::temporaryDirectory(),
        );
    }

    private static function configContents(): string
    {
        return \sprintf(
            <<<'PHP'
<?php

declare(strict_types=1);

use Ergebnis\Rector\Config;

return Config\Factory::fromRuleSet(%s::create())
    ->withCache(%s)
    ->withPaths([
        %s,
    ]);

PHP
            ,
            '\\' . self::ruleSetClassName(),
            \var_export(self::temporaryDirectory() . '/cache', true),
            \var_export(self::examplePath(), true),
        );
    }

    private static function exampleContents(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace Example;

final class Example
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}

PHP;
    }
}
