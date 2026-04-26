<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\HeadingOrderRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
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

        yield 'invalid headings' => [__DIR__.'/Fixtures/invalid/headings_jump.html.twig', ['HeadingOrder.HeadingOrder.Invalid:1:1' => 'Heading level jumped from h1 to h3.']];
    }
}
