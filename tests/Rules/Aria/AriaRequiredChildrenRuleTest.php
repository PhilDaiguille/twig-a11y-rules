<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaRequiredChildrenRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Aria\AriaRequiredChildrenRule
 */
final class AriaRequiredChildrenRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaRequiredChildrenRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valid list' => [__DIR__.'/Fixtures/valid/list_with_items.html.twig', []];

        yield 'missing children' => [
            __DIR__.'/Fixtures/invalid/list_missing_items.html.twig',
            [
                'AriaRequiredChildren.AriaRequiredChildren.MissingChild:1:1' => 'Role list must contain at least one of: listitem.',
            ],
        ];
    }
}
