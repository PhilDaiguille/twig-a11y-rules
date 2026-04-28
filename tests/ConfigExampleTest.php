<?php

declare(strict_types=1);

namespace TwigA11y\Tests;

use PHPUnit\Framework\TestCase;
use TwigCsFixer\Config\Config;

/**
 * @internal
 *
 * @coversNothing
 */
final class ConfigExampleTest extends TestCase
{
    public function testExampleConfigCanBeLoaded(): void
    {
        $config = require dirname(__DIR__).'/.twig-cs-fixer.php';

        $this->assertInstanceOf(Config::class, $config);
    }
}
