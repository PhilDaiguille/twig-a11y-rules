<?php

declare(strict_types=1);

namespace TwigA11y\Tests\Rules\Media;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TwigA11y\Rules\Media\AudioControlsRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(AudioControlsRule::class)]
final class AudioControlsRuleTest extends AbstractRuleTestCase
{
    /** @param array<string, string> $expectedErrors */
    #[DataProvider('provideFixtures')]
    public function testRule(string $fixture, array $expectedErrors): void
    {
        $this->checkRule(new AudioControlsRule(), $expectedErrors, $fixture);
    }

    /** @return iterable<string, array{0:string,1:array<string,string>}> */
    public static function provideFixtures(): iterable
    {
        yield 'audio with controls' => [__DIR__.'/Fixtures/valid/audio_with_controls.html.twig', []];

        yield 'audio missing controls' => [__DIR__.'/Fixtures/invalid/audio_missing_controls.html.twig', [
            'AudioControls.MissingControls:2:1' => '<audio> element must have a controls attribute to be operable by keyboard users (WCAG 1.2.1).',
        ]];

        yield 'multiple audio missing controls' => [__DIR__.'/Fixtures/invalid/audio_multiple_missing_controls.html.twig', [
            'AudioControls.MissingControls:2:1' => '<audio> element must have a controls attribute to be operable by keyboard users (WCAG 1.2.1).',
            'AudioControls.MissingControls#2:5:1' => '<audio> element must have a controls attribute to be operable by keyboard users (WCAG 1.2.1).',
        ]];
    }
}
