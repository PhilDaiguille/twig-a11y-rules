<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\AutoplayRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AutoplayRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AutoplayRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'autoplay not muted' => [__DIR__.'/Fixtures/invalid/autoplay_no_muted.html.twig', ['Autoplay.Autoplay.NotMuted:1:1' => 'Autoplaying media should be muted.']];

        yield 'ok' => [__DIR__.'/Fixtures/valid/img_with_alt.html.twig', []];
    }
}
