<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\InputLabelRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[\PHPUnit\Framework\Attributes\CoversNothing]
/** @internal */
final class InputLabelRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<null|string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new InputLabelRule(), $expectedErrors, $fixture);
    }

    /**
     * @return iterable<string, array{0:string,1:array<null|string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'input with label for' => [
            __DIR__.'/Fixtures/valid/input_with_label.html.twig',
            [],
        ];

        yield 'input without label or aria' => [
            __DIR__.'/Fixtures/invalid/input_no_label.html.twig',
            ['InputLabel.InputLabel.MissingLabel:3:1' => 'Input element must have an associated <label> or an aria-label.'],
        ];
    }
}
