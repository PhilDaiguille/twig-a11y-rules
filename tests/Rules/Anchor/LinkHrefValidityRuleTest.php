<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Anchor;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Anchor\LinkHrefValidityRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(LinkHrefValidityRule::class)]
final class LinkHrefValidityRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new LinkHrefValidityRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid real href' => [__DIR__.'/Fixtures/valid/link_with_real_href.html.twig', []];

        yield 'valid fragment href' => [__DIR__.'/Fixtures/valid/link_with_fragment_href.html.twig', []];

        yield 'valid role button without href' => [__DIR__.'/Fixtures/valid/link_role_button_no_href.html.twig', []];

        yield 'valid dynamic href' => [__DIR__.'/Fixtures/valid/link_with_dynamic_href.html.twig', []];

        yield 'missing href' => [__DIR__.'/Fixtures/invalid/link_missing_href.html.twig', [
            'LinkHrefValidity.LinkHref.MissingHref:2:1' => 'Anchor elements should include a valid href attribute.',
        ]];

        yield 'empty href' => [__DIR__.'/Fixtures/invalid/link_empty_href.html.twig', [
            'LinkHrefValidity.LinkHref.EmptyHref:2:1' => 'Anchor elements should not use an empty href attribute.',
        ]];

        yield 'placeholder href hash' => [__DIR__.'/Fixtures/invalid/link_placeholder_hash.html.twig', [
            'LinkHrefValidity.LinkHref.PlaceholderHref:2:1' => 'Anchor elements should use a real destination href instead of placeholder links.',
        ]];

        yield 'placeholder href js' => [__DIR__.'/Fixtures/invalid/link_placeholder_js.html.twig', [
            'LinkHrefValidity.LinkHref.PlaceholderHref:2:1' => 'Anchor elements should use a real destination href instead of placeholder links.',
        ]];
    }
}
