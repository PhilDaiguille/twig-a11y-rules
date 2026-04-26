<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaHiddenFocusRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class AriaHiddenFocusRuleTest extends AbstractRuleTestCase
{
    /** @param array<string|null> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaHiddenFocusRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string|null>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid non focusable' => [__DIR__.'/Fixtures/valid/aria_hidden_non_focusable.html.twig', []];
        yield 'invalid focusable' => [__DIR__.'/Fixtures/invalid/aria_hidden_focus.html.twig', ['AriaHiddenFocus.AriaHiddenFocus.HiddenFocusable:1:1' => 'Focusable element should not be aria-hidden.']];
    }
}
