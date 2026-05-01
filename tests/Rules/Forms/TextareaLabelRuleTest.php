<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\TextareaLabelRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(TextareaLabelRule::class)]
final class TextareaLabelRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new TextareaLabelRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid textarea' => [__DIR__.'/Fixtures/valid/textarea_with_label.html.twig', []];

        yield 'valid textarea with aria-labelledby' => [__DIR__.'/Fixtures/valid/textarea_aria_labelledby.html.twig', []];

        yield 'invalid textarea' => [__DIR__.'/Fixtures/invalid/textarea_no_label.html.twig', ['TextareaLabel.TextareaLabel.Missing:2:1' => 'Textarea must have an associated <label>.']];
    }
}
