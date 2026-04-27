<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\PageHeadingOneRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class PageHeadingOneRuleTest extends AbstractRuleTestCase
{
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new PageHeadingOneRule(), $expectedErrors, $fixture);
    }

    public static function provideFixtures(): iterable
    {
        yield 'has h1' => [
            __DIR__.'/Fixtures/valid/with_title.html.twig',
            [],
        ];

        yield 'no h1' => [
            __DIR__.'/Fixtures/invalid/no_h1.html.twig',
            ['PageHeadingOne.PageHeadingOne.Missing:1:1' => 'Document should include at least one non-empty <h1> heading.'],
        ];
    }
}
