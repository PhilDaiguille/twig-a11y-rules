<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\MetaCharsetRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(MetaCharsetRule::class)]
final class MetaCharsetRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new MetaCharsetRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'meta charset present' => [__DIR__.'/Fixtures/valid/meta_charset_present.html.twig', []];

        yield 'meta charset via http-equiv' => [__DIR__.'/Fixtures/valid/meta_charset_http_equiv.html.twig', []];

        yield 'meta charset missing' => [__DIR__.'/Fixtures/invalid/meta_charset_missing.html.twig', [
            'MetaCharset.MissingCharset:1:1' => 'Full-page document is missing a character encoding declaration (<meta charset="utf-8">) (RGAA 8.8, WCAG 4.1.1).',
        ]];
    }
}
