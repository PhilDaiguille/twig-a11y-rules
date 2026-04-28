<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\MetaRefreshRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class MetaRefreshRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new MetaRefreshRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'non-zero refresh' => [
            __DIR__.'/Fixtures/invalid/meta_refresh_timeout.html.twig',
            ['MetaRefresh.MetaRefresh.NonZeroTimeout:1:1' => '<meta http-equiv="refresh"> with a non-zero timeout (30) causes automatic page refresh (WCAG 2.2.1).'],
        ];

        yield 'zero refresh allowed' => [__DIR__.'/Fixtures/valid/meta_refresh_zero.html.twig', []];
    }
}
