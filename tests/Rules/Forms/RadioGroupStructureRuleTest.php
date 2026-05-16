<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\RadioGroupStructureRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(RadioGroupStructureRule::class)]
final class RadioGroupStructureRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new RadioGroupStructureRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'radio group in fieldset' => [__DIR__.'/Fixtures/valid/radio_group_in_fieldset.html.twig', []];

        yield 'radio group in radiogroup' => [__DIR__.'/Fixtures/valid/radio_group_in_radiogroup.html.twig', []];

        yield 'radio group missing structure' => [__DIR__.'/Fixtures/invalid/radio_group_missing_structure.html.twig', [
            'RadioGroupStructure.RadioGroupStructure.MissingGroup:2:1' => 'Radio inputs sharing name "contact" should be grouped inside a <fieldset> or a container with role="radiogroup".',
        ]];
    }
}
