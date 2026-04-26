<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaRequiredAttrRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class AriaRequiredAttrRuleTest extends AbstractRuleTestCase
{
    /** @param array<string|null> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaRequiredAttrRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string|null>}> */
    public static function provideFixtures(): iterable
    {
        yield 'missing required attr' => [__DIR__.'/Fixtures/invalid/required_attr_missing.html.twig', ['AriaRequiredAttr.AriaRequired.Missing:1:6' => 'Role "img" requires attribute "alt".']];
        yield 'no role' => [__DIR__.'/Fixtures/valid/role_none.html.twig', []];
    }
}
