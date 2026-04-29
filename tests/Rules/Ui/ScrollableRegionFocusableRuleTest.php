<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Ui;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Ui\ScrollableRegionFocusableRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Ui\ScrollableRegionFocusableRule
 */
final class ScrollableRegionFocusableRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new ScrollableRegionFocusableRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<(array<int, array<string, string>>|array<int, string>)>
     */
    public static function provideFixtures(): iterable
    {
        yield 'scrollable without tabindex' => [__DIR__.'/Fixtures/invalid/scrollable_no_tabindex.html.twig', [
            'ScrollableRegionFocusable.Scrollable.Focusable:1:1' => 'Scrollable region with overflow must be keyboard-focusable via tabindex.',
        ]];
    }
}
