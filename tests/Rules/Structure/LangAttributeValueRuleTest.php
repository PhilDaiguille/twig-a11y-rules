<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\LangAttributeValueRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class LangAttributeValueRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<string, string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new LangAttributeValueRule(), $expectedErrors, $fixture);
    }

    /**
     * @return iterable<string, array{0: string, 1: array<string, string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'valid lang en' => [
            __DIR__.'/Fixtures/valid/lang_valid_en.html.twig',
            [],
        ];

        yield 'valid lang fr-CA' => [
            __DIR__.'/Fixtures/valid/lang_valid_fr_ca.html.twig',
            [],
        ];

        yield 'valid lang zh-Hant-TW' => [
            __DIR__.'/Fixtures/valid/lang_valid_zh_hant_tw.html.twig',
            [],
        ];

        yield 'invalid lang xx' => [
            __DIR__.'/Fixtures/invalid/lang_invalid_value.html.twig',
            ['LangAttributeValue.LangAttributeValue.InvalidLang:3:1' => 'The lang attribute value "xx" is not a valid BCP 47 language tag (invalid primary subtag "xx").'],
        ];

        yield 'invalid primary subtag' => [
            __DIR__.'/Fixtures/invalid/lang_invalid_primary_subtag.html.twig',
            ['LangAttributeValue.LangAttributeValue.InvalidLang:3:1' => 'The lang attribute value "not-a-language" is not a valid BCP 47 language tag (invalid primary subtag "not").'],
        ];
    }
}
