<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\TableCaptionMissingRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(TableCaptionMissingRule::class)]
final class TableCaptionMissingRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new TableCaptionMissingRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'data table with caption' => [__DIR__.'/Fixtures/valid/table_with_caption.html.twig', []];

        yield 'layout table without caption' => [__DIR__.'/Fixtures/valid/layout_table_without_caption.html.twig', []];

        yield 'data table missing caption' => [__DIR__.'/Fixtures/invalid/data_table_missing_caption.html.twig', [
            'TableCaptionMissing.TableCaption.MissingCaption:2:1' => 'Data tables should include a non-empty <caption> element.',
        ]];
    }
}
