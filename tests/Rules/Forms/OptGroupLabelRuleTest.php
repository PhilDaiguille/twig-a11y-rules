<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Forms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Forms\OptGroupLabelRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(OptGroupLabelRule::class)]
final class OptGroupLabelRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new OptGroupLabelRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'optgroup with label' => [__DIR__.'/Fixtures/valid/optgroup_with_label.html.twig', []];

        yield 'optgroup missing label' => [__DIR__.'/Fixtures/invalid/optgroup_missing_label.html.twig', [
            'OptGroupLabel.MissingLabel:3:5' => '<optgroup> must have a non-empty label attribute to identify the group (WCAG 1.3.1).',
        ]];

        yield 'multiple optgroups missing labels' => [__DIR__.'/Fixtures/invalid/optgroup_multiple_missing_labels.html.twig', [
            'OptGroupLabel.MissingLabel:3:5' => '<optgroup> must have a non-empty label attribute to identify the group (WCAG 1.3.1).',
            'OptGroupLabel.MissingLabel#2:6:5' => '<optgroup> must have a non-empty label attribute to identify the group (WCAG 1.3.1).',
        ]];
    }
}
