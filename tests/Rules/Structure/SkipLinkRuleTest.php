<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\SkipLinkRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

#[CoversNothing]
/** @internal */
final class SkipLinkRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new SkipLinkRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid skip link' => [__DIR__.'/Fixtures/valid/skip_link.html.twig', []];

        yield 'valid main id' => [__DIR__.'/Fixtures/valid/main_id.html.twig', []];

        yield 'missing skip link' => [__DIR__.'/Fixtures/invalid/no_skip_link.html.twig', [
            'SkipLink.SkipLink.Missing:1:1' => 'Page should include a skip link to bypass navigation',
        ]];
    }
}
