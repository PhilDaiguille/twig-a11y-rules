<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\InputTypeRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class InputTypeRuleTest extends AbstractRuleTestCase
{
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new InputTypeRule(), $expectedErrors, $fixture);
    }

    public static function provideFixtures(): iterable
    {
        yield 'with autocomplete' => [__DIR__.'/Fixtures/valid/input_with_autocomplete.html.twig', []];

        yield 'missing autocomplete' => [
            __DIR__.'/Fixtures/invalid/input_missing_autocomplete.html.twig',
            ['InputType.InputType.MissingAutocomplete:1:1' => 'Input of type "email" should include an autocomplete attribute.'],
        ];
    }
}
