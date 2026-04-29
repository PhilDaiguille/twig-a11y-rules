<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\TableDuplicateNameRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Structure\TableDuplicateNameRule
 */
final class TableDuplicateNameRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new TableDuplicateNameRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid different summary and caption' => [__DIR__.'/Fixtures/valid/table_caption_and_summary_different.html.twig', []];

        yield 'duplicate summary and caption' => [__DIR__.'/Fixtures/invalid/table_duplicate_name.html.twig', [
            'TableDuplicateName.TableDuplicate.Duplicate:1:1' => 'Table summary duplicates caption content; provide distinct descriptions.',
        ]];
    }
}
