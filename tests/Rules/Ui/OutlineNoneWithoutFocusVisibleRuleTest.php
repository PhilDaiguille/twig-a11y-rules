<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Ui;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Ui\OutlineNoneWithoutFocusVisibleRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversNothing]
final class OutlineNoneWithoutFocusVisibleRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new OutlineNoneWithoutFocusVisibleRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valide avec focus-visible' => [
            __DIR__.'/Fixtures/valid/outline_none_with_focus_visible.html.twig',
            [],
        ];

        yield 'invalide, pas de focus-visible' => [
            __DIR__.'/Fixtures/invalid/outline_none_no_focus_visible.html.twig',
            [
                'OutlineNoneWithoutFocusVisible.OutlineNoneNoFocusVisible:2:1' => 'Using outline:none/0 without focus-visible compensation.',
            ],
        ];
    }
}
