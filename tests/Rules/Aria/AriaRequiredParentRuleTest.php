<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaRequiredParentRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class AriaRequiredParentRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaRequiredParentRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valid menu' => [__DIR__.'/Fixtures/valid/menu_with_items.html.twig', []];

        yield 'missing parent' => [
            __DIR__.'/Fixtures/invalid/menuitem_missing_parent.html.twig',
            [
                'AriaRequiredParent.AriaRequiredParent.MissingParent:1:1' => 'Role menuitem must be contained in one of: menu, menubar.',
            ],
        ];
    }
}
