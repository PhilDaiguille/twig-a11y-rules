<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\DuplicateAccessKeyRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DuplicateAccessKeyRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new DuplicateAccessKeyRule(), $expectedErrors, $fixture);
    }

    /**
     * @return iterable<string, array{0: string, 1: array<string, string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'unique accesskeys' => [
            __DIR__.'/Fixtures/valid/accesskey_unique.html.twig',
            [],
        ];

        yield 'duplicate accesskeys' => [
            __DIR__.'/Fixtures/invalid/accesskey_duplicate.html.twig',
            ['DuplicateAccessKey.DuplicateAccessKey.Duplicate:1:1' => 'Duplicate accesskey value "h" found. Each accesskey must be unique within a page.'],
        ];
    }
}
