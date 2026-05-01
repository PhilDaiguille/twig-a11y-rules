<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\PAsHeadingRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(PAsHeadingRule::class)]
final class PAsHeadingRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new PAsHeadingRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid normal paragraph' => [__DIR__.'/Fixtures/valid/p_normal_paragraph.html.twig', []];

        yield 'invalid bold paragraph' => [
            __DIR__.'/Fixtures/invalid/p_as_heading_bold.html.twig',
            ['PAsHeading.PAsHeading.FakeHeading:1:1' => 'Avoid using a <p> with bold/large-font styling as a heading; use a semantic heading element (<h1>–<h6>) instead.'],
        ];

        yield 'invalid large font paragraph' => [
            __DIR__.'/Fixtures/invalid/p_as_heading_large_font.html.twig',
            ['PAsHeading.PAsHeading.FakeHeading:1:1' => 'Avoid using a <p> with bold/large-font styling as a heading; use a semantic heading element (<h1>–<h6>) instead.'],
        ];
    }
}
