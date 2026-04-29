<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaValidAttrValueRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Aria\AriaValidAttrValueRule
 */
final class AriaValidAttrValueRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaValidAttrValueRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valid values' => [__DIR__.'/Fixtures/valid/aria_valid_values.html.twig', []];

        yield 'invalid value' => [
            __DIR__.'/Fixtures/invalid/aria_invalid_value.html.twig',
            [
                'AriaValidAttrValue.AriaValidAttrValue.InvalidValue:1:1' => 'Attribute aria-hidden has invalid value "maybe".',
            ],
        ];
    }
}
