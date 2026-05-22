<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\RadioGroupAccessibleNameRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(RadioGroupAccessibleNameRule::class)]
final class RadioGroupAccessibleNameRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new RadioGroupAccessibleNameRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'fieldset radio group with legend' => [__DIR__.'/Fixtures/valid/radio_group_in_fieldset.html.twig', []];

        yield 'radiogroup with aria label' => [__DIR__.'/Fixtures/valid/radiogroup_with_aria_label.html.twig', []];

        yield 'fieldset radio group missing legend' => [__DIR__.'/Fixtures/invalid/radio_group_fieldset_missing_legend.html.twig', [
            'RadioGroupAccessibleName.RadioGroupAccessibleName.MissingLegend:2:1' => 'Fieldsets containing radio groups should provide a non-empty <legend>.',
        ]];

        yield 'radiogroup missing accessible name' => [__DIR__.'/Fixtures/invalid/radiogroup_missing_accessible_name.html.twig', [
            'RadioGroupAccessibleName.RadioGroupAccessibleName.MissingName:2:1' => 'Containers with role="radiogroup" should have an accessible name via aria-label or aria-labelledby.',
        ]];
    }
}
