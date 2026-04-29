<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\DuplicateIdRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Structure\DuplicateIdRule
 */
final class DuplicateIdRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new DuplicateIdRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no duplicate' => [__DIR__.'/Fixtures/valid/valid.html.twig', []];

        yield 'duplicate ids' => [
            __DIR__.'/Fixtures/invalid/duplicate_ids.html.twig',
            [
                'DuplicateId.DuplicateId.Duplicate:1:1' => 'Duplicate id "foo" found in document.',
            ],
        ];
    }
}
