<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Structure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Structure\BannedTagsRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(BannedTagsRule::class)]
final class BannedTagsRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new BannedTagsRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'valid content' => [__DIR__.'/Fixtures/valid/no_banned.html.twig', []];

        yield 'invalid marquee' => [__DIR__.'/Fixtures/invalid/has_marquee.html.twig', ['BannedTags.BannedTags.Used:1:1' => 'Banned tag used (e.g. <marquee> or <blink>).']];
    }
}
