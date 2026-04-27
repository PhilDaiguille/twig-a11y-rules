<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\MetaViewportRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
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
        yield 'bad viewport' => [__DIR__.'/Fixtures/invalid/meta_viewport_bad.html.twig', ['MetaViewport.MetaViewport.UserScalable:1:1' => 'Avoid using user-scalable=no in the viewport meta.']];

        yield 'ok' => [__DIR__.'/Fixtures/valid/no_banned.html.twig', []];
    }

    public function testRuleWorksWhenTheSameInstanceIsReusedAcrossFiles(): void
    {
        $rule = new MetaViewportRule();

        $this->checkRule($rule, [], __DIR__.'/Fixtures/valid/no_banned.html.twig');
        $this->checkRule($rule, ['MetaViewport.MetaViewport.UserScalable:1:1' => 'Avoid using user-scalable=no in the viewport meta.'], __DIR__.'/Fixtures/invalid/meta_viewport_bad.html.twig');
    }
}
