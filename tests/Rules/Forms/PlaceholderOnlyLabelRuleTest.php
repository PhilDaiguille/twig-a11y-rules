<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\PlaceholderOnlyLabelRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(PlaceholderOnlyLabelRule::class)]
final class PlaceholderOnlyLabelRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new PlaceholderOnlyLabelRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'placeholder with label' => [__DIR__.'/Fixtures/valid/placeholder_with_label.html.twig', []];

        yield 'placeholder with aria label' => [__DIR__.'/Fixtures/valid/placeholder_with_aria_label.html.twig', []];

        yield 'placeholder only input' => [__DIR__.'/Fixtures/invalid/placeholder_only_input.html.twig', [
            'PlaceholderOnlyLabel.PlaceholderOnlyLabel.MissingLabel:2:1' => 'Form field appears to rely on placeholder text instead of a proper label.',
        ]];

        yield 'placeholder only textarea' => [__DIR__.'/Fixtures/invalid/placeholder_only_textarea.html.twig', [
            'PlaceholderOnlyLabel.PlaceholderOnlyLabel.MissingLabel:2:1' => 'Form field appears to rely on placeholder text instead of a proper label.',
        ]];
    }
}
