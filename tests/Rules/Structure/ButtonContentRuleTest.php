<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\ButtonContentRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ButtonContentRuleTest extends AbstractRuleTestCase
{
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new ButtonContentRule(), $expectedErrors, $fixture);
    }

    public static function provideFixtures(): iterable
    {
        yield 'button with text' => [
            __DIR__.'/Fixtures/valid/button_with_text.html.twig',
            [],
        ];

        yield 'button empty without aria' => [
            __DIR__.'/Fixtures/invalid/button_empty_no_aria.html.twig',
            ['ButtonContent.ButtonContent.MissingContent:3:1' => 'Button element without textual content must have an aria-label.'],
        ];
    }
}
