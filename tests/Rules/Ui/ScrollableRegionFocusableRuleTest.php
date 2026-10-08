<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Ui;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Ui\ScrollableRegionFocusableRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(ScrollableRegionFocusableRule::class)]
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
     * @return iterable<string, array{string, array<string, string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'scrollable without tabindex' => [
            __DIR__ . '/Fixtures/invalid/scrollable_no_tabindex.html.twig',
            [
                'ScrollableRegionFocusable.Focusable:1:1' => 'Scrollable region with overflow must be keyboard-focusable via tabindex.',
            ],
        ];
    }
}
