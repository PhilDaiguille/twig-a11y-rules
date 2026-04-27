<?php

declare(strict_types=1);

namespace TwigA11y\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use TwigCsFixer\Config\Config;

#[CoversNothing]
/** @internal */
final class ConfigExampleTest extends TestCase
{
    public function testExampleConfigCanBeLoaded(): void
    {
        $config = require dirname(__DIR__).'/.twig-cs-fixer.php';

        self::assertInstanceOf(Config::class, $config);
    }
}
