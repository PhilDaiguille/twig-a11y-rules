<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\AbbrTitleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(AbbrTitleRule::class)]
final class AbbrTitleRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AbbrTitleRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'abbr with title' => [__DIR__.'/Fixtures/valid/abbr_with_title.html.twig', []];

        yield 'abbr missing title' => [__DIR__.'/Fixtures/invalid/abbr_missing_title.html.twig', [
            'AbbrTitle.MissingTitle:2:8' => '<abbr> element must have a non-empty title attribute providing the expansion (RGAA 9.4).',
        ]];

        yield 'abbr empty title' => [__DIR__.'/Fixtures/invalid/abbr_empty_title.html.twig', [
            'AbbrTitle.MissingTitle:2:8' => '<abbr> element must have a non-empty title attribute providing the expansion (RGAA 9.4).',
        ]];
    }
}
