<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaValidAttrRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class AriaValidAttrRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaValidAttrRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valid aria attrs' => [__DIR__.'/Fixtures/valid/aria_valid_attr.html.twig', []];

        yield 'invalid aria attr' => [
            __DIR__.'/Fixtures/invalid/aria_invalid_attr.html.twig',
            [
                'AriaValidAttr.AriaValidAttr.InvalidAttr:1:1' => 'Attribute aria-foo is not a valid ARIA attribute.',
            ],
        ];
    }
}
