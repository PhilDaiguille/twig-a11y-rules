<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Aria;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Aria\AriaRequiredAttrRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(AriaRequiredAttrRule::class)]
final class AriaRequiredAttrRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AriaRequiredAttrRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'missing required attr' => [__DIR__.'/Fixtures/invalid/required_attr_missing.html.twig', ['AriaRequiredAttr.AriaRequired.Missing:1:1' => 'Role "img" requires attribute "alt".']];

        yield 'no role' => [__DIR__.'/Fixtures/valid/role_none.html.twig', []];

        yield 'textbox with aria-labelledby is valid' => [__DIR__.'/Fixtures/valid/textbox_with_aria_labelledby.html.twig', []];
    }
}
