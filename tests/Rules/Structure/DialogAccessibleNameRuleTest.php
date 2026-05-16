<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\DialogAccessibleNameRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(DialogAccessibleNameRule::class)]
final class DialogAccessibleNameRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new DialogAccessibleNameRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'dialog with aria label' => [__DIR__.'/Fixtures/valid/dialog_with_aria_label.html.twig', []];

        yield 'dialog with aria labelledby' => [__DIR__.'/Fixtures/valid/dialog_with_aria_labelledby.html.twig', []];

        yield 'role dialog with aria labelledby' => [__DIR__.'/Fixtures/valid/role_dialog_with_aria_labelledby.html.twig', []];

        yield 'dialog without name' => [__DIR__.'/Fixtures/invalid/dialog_without_name.html.twig', [
            'DialogAccessibleName.DialogAccessibleName.Missing:2:1' => 'Dialog element must have an accessible name via aria-label or aria-labelledby.',
        ]];

        yield 'role alertdialog without name' => [__DIR__.'/Fixtures/invalid/alertdialog_without_name.html.twig', [
            'DialogAccessibleName.DialogAccessibleName.Missing:2:1' => 'Dialog element must have an accessible name via aria-label or aria-labelledby.',
        ]];
    }
}
