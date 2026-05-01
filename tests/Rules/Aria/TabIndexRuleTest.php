<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\TabIndexRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(TabIndexRule::class)]
final class TabIndexRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<null|string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new TabIndexRule(), $expectedErrors, $fixture);
    }

    /**
     * @return iterable<string, array{0:string,1:array<null|string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'tabindex zero' => [
            __DIR__.'/Fixtures/valid/tabindex_zero.html.twig',
            [],
        ];

        yield 'tabindex positive' => [
            __DIR__.'/Fixtures/invalid/tabindex_positive.html.twig',
            ['TabIndex.TabIndex.PositiveTabindex:4:6' => 'Avoid positive tabindex values — use 0 or manage focus order differently.'],
        ];
    }
}
