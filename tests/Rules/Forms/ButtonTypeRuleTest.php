<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\ButtonTypeRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(ButtonTypeRule::class)]
final class ButtonTypeRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new ButtonTypeRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'button with explicit type' => [__DIR__.'/Fixtures/valid/button_with_explicit_type.html.twig', []];

        yield 'button outside a form needs no type' => [__DIR__.'/Fixtures/valid/button_outside_form_without_type.html.twig', []];

        yield 'button without type' => [__DIR__.'/Fixtures/invalid/button_without_type.html.twig', [
            'ButtonType.ButtonType.MissingType:3:3' => 'Button inside a form should declare an explicit type attribute.',
        ]];
    }
}
