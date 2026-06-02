<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\VideoDescriptionTrackRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(VideoDescriptionTrackRule::class)]
final class VideoDescriptionTrackRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new VideoDescriptionTrackRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'video with descriptions track' => [__DIR__.'/Fixtures/valid/video_with_descriptions.html.twig', []];

        yield 'video unclosed tag — no descriptions required' => [__DIR__.'/Fixtures/valid/video_unclosed_no_descriptions_needed.html.twig', []];

        yield 'video missing descriptions track' => [__DIR__.'/Fixtures/invalid/video_missing_descriptions.html.twig', [
            'VideoDescriptionTrack.MissingDescriptions:1:1' => 'Video should have an audio description track (<track kind="descriptions">) (WCAG 1.2.5).',
        ]];
    }
}
