<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\VideoTrackRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(VideoTrackRule::class)]
final class VideoTrackRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new VideoTrackRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'video with captions' => [__DIR__.'/Fixtures/valid/video_with_captions.html.twig', []];

        yield 'video without captions' => [
            __DIR__.'/Fixtures/invalid/video_no_captions.html.twig',
            ['VideoTrack.VideoTrack.MissingCaptions:1:1' => 'Video should have captions (track kind="captions").'],
        ];
    }
}
