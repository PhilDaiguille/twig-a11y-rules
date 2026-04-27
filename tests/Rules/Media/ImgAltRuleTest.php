<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\ImgAltRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class ImgAltRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new ImgAltRule(), $expectedErrors, $fixture);
    }

    /**
     * Data provider for fixtures.
     *
     * @return iterable<string, array{0:string,1:array<string,string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'img with alt' => [
            __DIR__.'/Fixtures/valid/img_with_alt.html.twig',
            [],
        ];

        yield 'img decorative empty alt' => [
            __DIR__.'/Fixtures/valid/img_with_empty_alt_decorative.html.twig',
            [],
        ];

        yield 'img with variable alt' => [
            __DIR__.'/Fixtures/valid/img_with_variable_alt.html.twig',
            ['ImgAlt.ImgAlt.DynamicAlt:1:1' => 'Alt attribute contains template expression; verify it is non-empty at runtime.'],
        ];

        yield 'img with long attributes' => [
            __DIR__.'/Fixtures/valid/img_with_long_attributes.html.twig',
            [],
        ];

        yield 'img without alt' => [
            __DIR__.'/Fixtures/invalid/img_no_alt.html.twig',
            ['ImgAlt.ImgAlt.MissingAlt:2:1' => 'Missing alt attribute on <img> tag.'],
        ];

        yield 'img with empty alt without role' => [
            __DIR__.'/Fixtures/invalid/img_empty_alt.html.twig',
            ['ImgAlt.ImgAlt.EmptyAlt:2:1' => 'Empty alt on <img> requires role="presentation" or role="none".'],
        ];

        yield 'img with interpolated alt (dynamic)' => [
            __DIR__.'/Fixtures/invalid/img_no_alt_interpolated.html.twig',
            ['ImgAlt.ImgAlt.DynamicAlt:4:5' => 'Alt attribute contains template expression; verify it is non-empty at runtime.'],
        ];

        yield 'img with dynamic empty alt' => [
            __DIR__.'/Fixtures/invalid/img_dynamic_empty_alt.html.twig',
            ['ImgAlt.ImgAlt.DynamicAlt:4:5' => 'Alt attribute contains template expression; verify it is non-empty at runtime.'],
        ];

        yield 'img split across tokens without alt' => [
            __DIR__.'/Fixtures/invalid/img_split_tokens_no_alt.html.twig',
            ['ImgAlt.ImgAlt.MissingAlt:5:5' => 'Missing alt attribute on <img> tag.'],
        ];
    }
}
