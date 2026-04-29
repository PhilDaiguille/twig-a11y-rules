<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\SelectLabelRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Forms\SelectLabelRule
 */
final class SelectLabelRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new SelectLabelRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid select' => [__DIR__.'/Fixtures/valid/select_with_label.html.twig', []];

        yield 'valid select with aria-labelledby' => [__DIR__.'/Fixtures/valid/select_with_aria_labelledby.html.twig', []];

        yield 'valid select with aria-label' => [__DIR__.'/Fixtures/valid/select_with_aria_label.html.twig', []];

        yield 'invalid select' => [__DIR__.'/Fixtures/invalid/select_no_label.html.twig', ['SelectLabel.SelectLabel.Missing:1:1' => 'Select element must have an associated <label>.']];
    }
}
