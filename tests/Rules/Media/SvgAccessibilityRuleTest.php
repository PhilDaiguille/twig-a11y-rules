<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\SvgAccessibilityRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(SvgAccessibilityRule::class)]
final class SvgAccessibilityRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new SvgAccessibilityRule(), $expectedErrors, $fixture);
    }

    /**
     * Data provider for fixtures.
     *
     * @return iterable<string, array{0:string,1:array<string,string>}>
     */
    public static function provideFixtures(): iterable
    {
        // Valid cases
        yield 'svg with aria-hidden (decorative)' => [
            __DIR__.'/Fixtures/valid/svg_decorative_aria_hidden.html.twig',
            [],
        ];

        yield 'svg with title' => [
            __DIR__.'/Fixtures/valid/svg_with_title.html.twig',
            [],
        ];

        yield 'svg with aria-label' => [
            __DIR__.'/Fixtures/valid/svg_with_aria_label.html.twig',
            [],
        ];

        yield 'svg with aria-labelledby' => [
            __DIR__.'/Fixtures/valid/svg_with_aria_labelledby.html.twig',
            [],
        ];

        yield 'svg with role=img and aria-label' => [
            __DIR__.'/Fixtures/valid/svg_role_img_with_aria_label.html.twig',
            [],
        ];

        yield 'svg with role=img and title' => [
            __DIR__.'/Fixtures/valid/svg_role_img_with_title.html.twig',
            [],
        ];

        // Invalid cases
        yield 'svg without accessible name' => [
            __DIR__.'/Fixtures/invalid/svg_no_accessible_name.html.twig',
            ['SvgAccessibility.SvgA11y.MissingAccessibleName:2:1' => 'SVG element is missing an accessible name. Add <title>, aria-label, aria-labelledby, or aria-hidden="true" if decorative.'],
        ];

        yield 'svg with role=img but no accessible name' => [
            __DIR__.'/Fixtures/invalid/svg_role_img_no_accessible_name.html.twig',
            ['SvgAccessibility.SvgA11y.MissingNameForRoleImg:2:1' => 'SVG with role="img" is missing an accessible name (<title>, aria-label, or aria-labelledby).'],
        ];
    }
}
