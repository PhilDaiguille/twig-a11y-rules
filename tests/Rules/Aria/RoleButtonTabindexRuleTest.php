<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\RoleButtonTabindexRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(RoleButtonTabindexRule::class)]
final class RoleButtonTabindexRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new RoleButtonTabindexRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'no interactive role attributes' => [__DIR__.'/Fixtures/valid/role_no_interactive_roles.html.twig', []];

        yield 'role keyword in twig expression — no html tags' => [__DIR__.'/Fixtures/valid/role_keyword_in_twig_no_tags.html.twig', []];

        yield 'interactive roles with tabindex' => [__DIR__.'/Fixtures/valid/role_interactive_with_tabindex.html.twig', []];

        yield 'TwigUX components with interactive roles are not flagged' => [__DIR__.'/Fixtures/valid/role_twigux_component.html.twig', []];

        yield 'role button missing tabindex' => [__DIR__.'/Fixtures/invalid/role_button_missing_tabindex.html.twig', [
            'RoleButtonTabindex.MissingTabindex:2:1' => '<div role="button"> is not natively focusable and must have tabindex="0" to be keyboard-reachable (WCAG 4.1.2, 2.1.1).',
        ]];

        yield 'role checkbox missing tabindex' => [__DIR__.'/Fixtures/invalid/role_checkbox_missing_tabindex.html.twig', [
            'RoleButtonTabindex.MissingTabindex:2:1' => '<span role="checkbox"> is not natively focusable and must have tabindex="0" to be keyboard-reachable (WCAG 4.1.2, 2.1.1).',
        ]];

        yield 'multiple elements missing tabindex' => [__DIR__.'/Fixtures/invalid/role_multiple_missing_tabindex.html.twig', [
            'RoleButtonTabindex.MissingTabindex:2:1' => '<div role="button"> is not natively focusable and must have tabindex="0" to be keyboard-reachable (WCAG 4.1.2, 2.1.1).',
            'RoleButtonTabindex.MissingTabindex#2:3:1' => '<span role="link"> is not natively focusable and must have tabindex="0" to be keyboard-reachable (WCAG 4.1.2, 2.1.1).',
        ]];
    }
}
