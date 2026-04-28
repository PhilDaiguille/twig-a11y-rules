<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class HeadingOrderRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new HeadingOrderRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid headings' => [__DIR__.'/Fixtures/valid/headings_ok.html.twig', []];

        yield 'valid headings (more)' => [__DIR__.'/Fixtures/valid/headings_ok_more.html.twig', []];

        yield 'invalid headings (simple jump)' => [__DIR__.'/Fixtures/invalid/headings_jump.html.twig', ['HeadingOrder.HeadingOrder.Invalid:1:1' => 'Heading level jumped from h1 to h3.']];

        yield 'invalid headings (with attrs)' => [__DIR__.'/Fixtures/invalid/headings_jump_attr.html.twig', ['HeadingOrder.HeadingOrder.Invalid:1:1' => 'Heading level jumped from h1 to h3.']];

        yield 'invalid headings (multiple jumps)' => [
            __DIR__.'/Fixtures/invalid/headings_multiple_jumps.html.twig',
            [
                'HeadingOrder.HeadingOrder.Invalid:1:1' => 'Heading level jumped from h1 to h3.',
                'HeadingOrder.HeadingOrder.Invalid#2:1:1' => 'Heading level jumped from h4 to h6.',
            ],
        ];
    }
}
