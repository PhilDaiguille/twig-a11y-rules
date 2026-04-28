<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class NoAutoplayAudioRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

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
