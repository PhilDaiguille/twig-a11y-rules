<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\AutocompleteValidRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AutocompleteValidRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AutocompleteValidRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<(array<int, array<string, string>>|array<int, string>)>
     */
    public static function provideFixtures(): iterable
    {
        yield 'invalid autocomplete' => [__DIR__.'/Fixtures/invalid/input_autocomplete_invalid.html.twig', [
            'AutocompleteValid.Autocomplete.Invalid:1:1' => 'Invalid autocomplete value "foo".',
        ]];

        yield 'dynamic autocomplete ignored' => [__DIR__.'/Fixtures/valid/input_autocomplete_dynamic.html.twig', []];
    }
}
