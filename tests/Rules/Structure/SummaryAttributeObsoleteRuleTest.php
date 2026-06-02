<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\SummaryAttributeObsoleteRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(SummaryAttributeObsoleteRule::class)]
final class SummaryAttributeObsoleteRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new SummaryAttributeObsoleteRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'table with caption no summary' => [__DIR__.'/Fixtures/valid/table_caption_no_summary.html.twig', []];

        yield 'table with obsolete summary' => [__DIR__.'/Fixtures/invalid/table_summary_obsolete.html.twig', [
            'SummaryAttributeObsolete.ObsoleteSummary:2:1' => 'The summary attribute on <table> is obsolete in HTML5. Use <caption> to describe the table (WCAG 4.1.1).',
        ]];

        yield 'multiple tables with obsolete summary' => [__DIR__.'/Fixtures/invalid/table_multiple_obsolete_summary.html.twig', [
            'SummaryAttributeObsolete.ObsoleteSummary:2:1' => 'The summary attribute on <table> is obsolete in HTML5. Use <caption> to describe the table (WCAG 4.1.1).',
            'SummaryAttributeObsolete.ObsoleteSummary#2:5:1' => 'The summary attribute on <table> is obsolete in HTML5. Use <caption> to describe the table (WCAG 4.1.1).',
        ]];
    }
}
