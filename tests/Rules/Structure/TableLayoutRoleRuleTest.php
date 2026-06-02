<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\TableLayoutRoleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(TableLayoutRoleRule::class)]
final class TableLayoutRoleRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new TableLayoutRoleRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no table element' => [__DIR__.'/Fixtures/valid/table_no_table_element.html.twig', []];

        yield 'unclosed table — no role needed' => [__DIR__.'/Fixtures/valid/table_unclosed_no_role_needed.html.twig', []];

        yield 'data table with th' => [__DIR__.'/Fixtures/valid/table_data_with_th.html.twig', []];

        yield 'layout table with role presentation' => [__DIR__.'/Fixtures/valid/table_layout_with_role.html.twig', []];

        yield 'layout table missing role' => [__DIR__.'/Fixtures/invalid/table_layout_missing_role.html.twig', [
            'TableLayoutRole.LayoutTableMissingRole:2:1' => '<table> without <th> appears to be a layout table and should have role="presentation" or role="none" (RGAA 5.3, WCAG 1.3.1).',
        ]];
    }
}
