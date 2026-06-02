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
        yield 'interactive roles with tabindex' => [__DIR__.'/Fixtures/valid/role_interactive_with_tabindex.html.twig', []];

        yield 'role button missing tabindex' => [__DIR__.'/Fixtures/invalid/role_button_missing_tabindex.html.twig', [
            'RoleButtonTabindex.MissingTabindex:2:1' => '<div role="button"> is not natively focusable and must have tabindex="0" to be keyboard-reachable (WCAG 4.1.2, 2.1.1).',
        ]];

        yield 'role checkbox missing tabindex' => [__DIR__.'/Fixtures/invalid/role_checkbox_missing_tabindex.html.twig', [
            'RoleButtonTabindex.MissingTabindex:2:1' => '<span role="checkbox"> is not natively focusable and must have tabindex="0" to be keyboard-reachable (WCAG 4.1.2, 2.1.1).',
        ]];
    }
}
