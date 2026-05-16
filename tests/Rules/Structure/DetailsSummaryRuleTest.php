<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\DetailsSummaryRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(DetailsSummaryRule::class)]
final class DetailsSummaryRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new DetailsSummaryRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'details with summary' => [__DIR__.'/Fixtures/valid/details_with_summary.html.twig', []];

        yield 'details without summary' => [__DIR__.'/Fixtures/invalid/details_without_summary.html.twig', [
            'DetailsSummary.DetailsSummary.MissingSummary:2:1' => 'Details element must contain a non-empty <summary>.',
        ]];

        yield 'details with empty summary' => [__DIR__.'/Fixtures/invalid/details_with_empty_summary.html.twig', [
            'DetailsSummary.DetailsSummary.EmptySummary:2:1' => 'Summary element inside <details> must have non-empty content.',
        ]];
    }
}
