<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\AriaInputFieldNameRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Forms\AriaInputFieldNameRule
 */
final class AriaInputFieldNameRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaInputFieldNameRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valid textbox with aria-label' => [__DIR__.'/Fixtures/valid/div_textbox_with_label.html.twig', []];

        yield 'invalid textbox no name' => [
            __DIR__.'/Fixtures/invalid/div_textbox_no_label.html.twig',
            [
                'AriaInputFieldName.AriaInputFieldName.MissingName:1:1' => 'role="textbox" element must have an accessible name (aria-label or aria-labelledby).',
            ],
        ];
    }
}
