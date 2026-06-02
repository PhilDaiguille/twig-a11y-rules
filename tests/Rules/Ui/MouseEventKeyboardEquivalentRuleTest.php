<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Ui;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Ui\MouseEventKeyboardEquivalentRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(MouseEventKeyboardEquivalentRule::class)]
final class MouseEventKeyboardEquivalentRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new MouseEventKeyboardEquivalentRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no mouse event handlers' => [__DIR__.'/Fixtures/valid/mouse_no_events.html.twig', []];

        yield 'mouse event keyword in twig — no html tags' => [__DIR__.'/Fixtures/valid/mouse_event_keyword_in_twig_no_tags.html.twig', []];

        yield 'mouse events with keyboard equivalents' => [__DIR__.'/Fixtures/valid/mouse_events_with_keyboard.html.twig', []];

        yield 'TwigUX components with mouse events are not flagged' => [__DIR__.'/Fixtures/valid/mouse_twigux_component.html.twig', []];

        yield 'onmouseover without onfocus' => [__DIR__.'/Fixtures/invalid/mouse_event_no_keyboard.html.twig', [
            'MouseEventKeyboardEquivalent.MouseOnlyEvent:2:1' => 'Mouse event handler "onmouseover" has no keyboard equivalent (onfocus) (WCAG 2.1.1).',
        ]];

        yield 'multiple mouse-only violations' => [__DIR__.'/Fixtures/invalid/mouse_multiple_violations.html.twig', [
            'MouseEventKeyboardEquivalent.MouseOnlyEvent:2:1' => 'Mouse event handler "onmouseover" has no keyboard equivalent (onfocus) (WCAG 2.1.1).',
            'MouseEventKeyboardEquivalent.MouseOnlyEvent#2:3:1' => 'Mouse event handler "onmouseover" has no keyboard equivalent (onfocus) (WCAG 2.1.1).',
        ]];
    }
}
