<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\NestedInteractiveRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class NestedInteractiveRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new NestedInteractiveRule(), $expectedErrors, $fixture);
    }

    /**
     * @return iterable<string, array{0: string, 1: array<string, string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'anchor without nested interactive' => [
            __DIR__.'/Fixtures/valid/anchor_no_nested_interactive.html.twig',
            [],
        ];

        yield 'button without nested interactive' => [
            __DIR__.'/Fixtures/valid/button_no_nested_interactive.html.twig',
            [],
        ];

        yield 'button inside anchor' => [
            __DIR__.'/Fixtures/invalid/button_inside_anchor.html.twig',
            ['NestedInteractive.NestedInteractive.InsideAnchor:1:1' => 'Interactive element <button> must not be nested inside an <a>.'],
        ];

        yield 'anchor inside button' => [
            __DIR__.'/Fixtures/invalid/anchor_inside_button.html.twig',
            ['NestedInteractive.NestedInteractive.InsideButton:1:1' => 'Interactive element <a> must not be nested inside a <button>.'],
        ];
    }
}
