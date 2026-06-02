<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Ui;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Ui\ClickableNonInteractiveRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(ClickableNonInteractiveRule::class)]
final class ClickableNonInteractiveRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new ClickableNonInteractiveRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no onclick handlers' => [__DIR__.'/Fixtures/valid/clickable_no_onclick.html.twig', []];

        yield 'onclick keyword in twig — no html tags' => [__DIR__.'/Fixtures/valid/clickable_onclick_in_twig_no_tags.html.twig', []];

        yield 'native interactive elements' => [__DIR__.'/Fixtures/valid/clickable_native_interactive.html.twig', []];

        yield 'div onclick without tabindex' => [__DIR__.'/Fixtures/invalid/clickable_div_no_tabindex.html.twig', [
            'ClickableNonInteractive.NonInteractiveOnclick:2:1' => '<div onclick="..."> is not keyboard-reachable. Use a <button>, or add tabindex="0" and a role (WCAG 4.1.2, 2.1.1).',
        ]];

        yield 'multiple clickable violations' => [__DIR__.'/Fixtures/invalid/clickable_multiple_violations.html.twig', [
            'ClickableNonInteractive.NonInteractiveOnclick:3:1' => '<div onclick="..."> is not keyboard-reachable. Use a <button>, or add tabindex="0" and a role (WCAG 4.1.2, 2.1.1).',
            'ClickableNonInteractive.NonInteractiveOnclick#2:4:1' => '<div onclick="..."> is not keyboard-reachable. Use a <button>, or add tabindex="0" and a role (WCAG 4.1.2, 2.1.1).',
        ]];
    }
}
