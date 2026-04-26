<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\MetaViewportRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class MetaViewportRuleTest extends AbstractRuleTestCase
{
    /** @param array<string|null> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new MetaViewportRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string|null>}> */
    public static function provideFixtures(): iterable
    {
        yield 'bad viewport' => [__DIR__.'/Fixtures/invalid/meta_viewport_bad.html.twig', ['MetaViewport.MetaViewport.UserScalable:1:1' => 'Avoid using user-scalable=no in the viewport meta.']];
        yield 'ok' => [__DIR__.'/Fixtures/valid/no_banned.html.twig', []];
    }
}
