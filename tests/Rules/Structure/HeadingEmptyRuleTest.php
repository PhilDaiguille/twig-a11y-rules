<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\HeadingEmptyRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class HeadingEmptyRuleTest extends AbstractRuleTestCase
{
    /** @param array<string|null> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new HeadingEmptyRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string|null>}> */
    public static function provideFixtures(): iterable
    {
        yield 'empty heading' => [__DIR__.'/Fixtures/invalid/heading_empty.html.twig', ['HeadingEmpty.HeadingEmpty.Empty:1:1' => 'Heading element should not be empty.']];
        yield 'non empty' => [__DIR__.'/Fixtures/valid/headings_ok.html.twig', []];
    }
}
