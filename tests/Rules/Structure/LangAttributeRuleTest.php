<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\LangAttributeRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class LangAttributeRuleTest extends AbstractRuleTestCase
{
    /**
     * @param array<null|string> $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new LangAttributeRule(), $expectedErrors, $fixture);
    }

    /**
     * @return iterable<string, array{0:string,1:array<null|string>}>
     */
    public static function provideFixtures(): iterable
    {
        yield 'html with lang' => [
            __DIR__.'/Fixtures/valid/html_with_lang.html.twig',
            [],
        ];

        yield 'html without lang' => [
            __DIR__.'/Fixtures/invalid/html_no_lang.html.twig',
            ['LangAttribute.LangAttribute.MissingLang:2:1' => 'The <html> element should have a lang attribute.'],
        ];
    }
}
