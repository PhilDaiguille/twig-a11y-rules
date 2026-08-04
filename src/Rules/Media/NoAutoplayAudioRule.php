<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Media\NoAutoplayAudioRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * @see NoAutoplayAudioRuleTest
 * @see NoAutoplayAudioRuleTest
 * @see NoAutoplayAudioRuleTest
 * @see NoAutoplayAudioRuleTest
 * @see NoAutoplayAudioRuleTest
 * @see NoAutoplayAudioRuleTest
 */
final class NoAutoplayAudioRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = strtolower($this->getFullContent($tokens));

        if (!str_contains($full, '<audio')) {
            return;
        }

        if (preg_match_all('/<audio\b([^>]*)>/i', $full, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                $attrs = $match[1];
                if (preg_match('/\bautoplay\b/i', $attrs) && !preg_match('/\bcontrols\b/i', $attrs)) {
                    $fake = $tokens->get(0);
                    $emit('Audio with autoplay must expose controls.', $fake, 'AutoplayAudio.NoControls');

                    return; // emit only once per file
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
