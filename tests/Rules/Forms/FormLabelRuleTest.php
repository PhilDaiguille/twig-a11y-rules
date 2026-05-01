<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\FormLabelRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(FormLabelRule::class)]
final class FormLabelRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new FormLabelRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid label with for' => [__DIR__.'/Fixtures/valid/label_with_for.html.twig', []];

        yield 'invalid empty label' => [__DIR__.'/Fixtures/invalid/label_empty.html.twig', ['FormLabel.FormLabel.InvalidLabel:2:1' => '<label> must have a for attribute or non-empty content.']];
    }
}
