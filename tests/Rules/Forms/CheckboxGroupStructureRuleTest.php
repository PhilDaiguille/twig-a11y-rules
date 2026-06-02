<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\CheckboxGroupStructureRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(CheckboxGroupStructureRule::class)]
final class CheckboxGroupStructureRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new CheckboxGroupStructureRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'checkbox group in fieldset' => [__DIR__.'/Fixtures/valid/checkbox_group_in_fieldset.html.twig', []];

        yield 'checkbox group with role group' => [__DIR__.'/Fixtures/valid/checkbox_group_role_group.html.twig', []];

        yield 'single checkbox — no grouping needed' => [__DIR__.'/Fixtures/valid/checkbox_single_no_group_needed.html.twig', []];

        yield 'edge cases — non-checkbox inputs, no-name, empty-name' => [__DIR__.'/Fixtures/valid/checkbox_edge_cases_no_group_needed.html.twig', []];

        yield 'checkbox group missing structure' => [__DIR__.'/Fixtures/invalid/checkbox_group_missing_structure.html.twig', [
            'CheckboxGroupStructure.MissingGroup:1:1' => 'Checkbox inputs sharing name "notify" should be grouped inside a <fieldset> or a container with role="group" (WCAG 1.3.1, RGAA 11.7).',
        ]];
    }
}
