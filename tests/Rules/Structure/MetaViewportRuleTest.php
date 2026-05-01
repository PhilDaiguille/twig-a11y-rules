<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\MetaViewportRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(MetaViewportRule::class)]
final class MetaViewportRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new MetaViewportRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        // This fixture is a fragment (no <html>/<body>) — rule is page-level
        // and should not emit on partials.
        yield 'bad viewport' => [__DIR__.'/Fixtures/invalid/meta_viewport_bad.html.twig', []];

        yield 'ok' => [__DIR__.'/Fixtures/valid/no_banned.html.twig', []];

        yield 'maximum-scale too low' => [
            __DIR__.'/Fixtures/invalid/meta_viewport_max_scale.html.twig',
            ['MetaViewport.MetaViewport.MaximumScale:1:1' => 'Avoid setting maximum-scale below 2 in the viewport meta (WCAG 1.4.4).'],
        ];

        yield 'maximum-scale acceptable' => [__DIR__.'/Fixtures/valid/meta_viewport_max_scale_ok.html.twig', []];
    }

    public function testRuleWorksWhenTheSameInstanceIsReusedAcrossFiles(): void
    {
        $rule = new MetaViewportRule();

        $this->checkRule($rule, [], __DIR__.'/Fixtures/valid/no_banned.html.twig');
        // fragment should still not trigger when same instance is reused
        $this->checkRule($rule, [], __DIR__.'/Fixtures/invalid/meta_viewport_bad.html.twig');
    }
}
