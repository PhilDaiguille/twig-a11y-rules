<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\InputTypeRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Forms\InputTypeRule
 */
final class InputTypeRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new InputTypeRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'with autocomplete' => [__DIR__.'/Fixtures/valid/input_with_autocomplete.html.twig', []];

        yield 'tel with autocomplete' => [__DIR__.'/Fixtures/valid/input_tel_with_autocomplete.html.twig', []];

        yield 'missing autocomplete' => [
            __DIR__.'/Fixtures/invalid/input_missing_autocomplete.html.twig',
            ['InputType.InputType.MissingAutocomplete:1:1' => 'Input of type "email" should include an autocomplete attribute (WCAG 1.3.5).'],
        ];

        yield 'tel missing autocomplete' => [
            __DIR__.'/Fixtures/invalid/input_tel_missing_autocomplete.html.twig',
            ['InputType.InputType.MissingAutocomplete:1:1' => 'Input of type "tel" should include an autocomplete attribute (WCAG 1.3.5).'],
        ];
    }
}
