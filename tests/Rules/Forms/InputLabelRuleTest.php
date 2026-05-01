<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(InputLabelRule::class)]
final class InputLabelRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<null|string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new InputLabelRule(), $expectedErrors, $fixture);
    }

    /**
     * @return iterable<string, array{0:string,1:array<null|string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'input with label for' => [
            __DIR__.'/Fixtures/valid/input_with_label.html.twig',
            [],
        ];

        yield 'input without label or aria' => [
            __DIR__.'/Fixtures/invalid/input_no_label.html.twig',
            ['InputLabel.InputLabel.MissingLabel:4:1' => 'Input element must have an associated <label> or an aria-label.'],
        ];

        // Hidden inputs should not trigger the rule
        yield 'input hidden' => [__DIR__.'/Fixtures/valid/input_hidden.html.twig', []];

        // Dynamic aria-label (Twig var) should be considered present
        yield 'input with aria variable' => [__DIR__.'/Fixtures/valid/input_with_aria_variable.html.twig', []];

        // Empty aria-label must NOT bypass the label check
        yield 'input with empty aria-label' => [
            __DIR__.'/Fixtures/invalid/input_empty_aria_label.html.twig',
            ['InputLabel.InputLabel.MissingLabel:4:1' => 'Input element must have an associated <label> or an aria-label.'],
        ];
    }
}
