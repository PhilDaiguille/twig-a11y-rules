<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaHiddenBodyRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class AriaHiddenBodyRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaHiddenBodyRule(), $expectedErrors, $fixture);
    }

    /**
     * @return \Iterator<array<int, (array<mixed>|string)>>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valid page' => [__DIR__.'/Fixtures/valid/role_valid.html.twig', []];

        yield 'body aria hidden' => [
            __DIR__.'/Fixtures/invalid/body_aria_hidden.html.twig',
            [
                'AriaHiddenBody.AriaHiddenBody.HiddenOnBody:1:1' => 'Do not set aria-hidden="true" on the <body> element.',
            ],
        ];
    }
}
