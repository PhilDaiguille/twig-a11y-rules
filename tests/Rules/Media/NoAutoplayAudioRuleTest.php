<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\NoAutoplayAudioRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @covers \TwigA11y\Rules\Media\NoAutoplayAudioRule
 */
final class NoAutoplayAudioRuleTest extends AbstractRuleTestCase
{
    /** @param array<null|string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new NoAutoplayAudioRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<null|string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'audio autoplay no controls' => [__DIR__.'/Fixtures/invalid/audio_autoplay_no_controls.html.twig', [
            'NoAutoplayAudio.AutoplayAudio.NoControls:1:1' => 'Audio with autoplay must expose controls.',
        ]];
    }
}
