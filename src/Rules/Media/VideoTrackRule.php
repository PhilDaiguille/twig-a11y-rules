<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class VideoTrackRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<video')) {
            return;
        }

        // Find all <video ...>...</video> blocks
        if (!preg_match_all('/<video\b([^>]*)>(.*?)<\/video>/is', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        foreach ($m as $set) {
            $content = $set[2];

            // If a <track kind="captions" exists inside the video block, OK
            if (preg_match('/<track\b[^>]*\bkind\s*=\s*(?:"|\')captions(?:"|\')/i', $content)) {
                continue;
            }

            // No captions found — report error at token 0 for determinism
            $token = $tokens->get(0);
            $emit('Video should have captions (track kind="captions").', $token, 'VideoTrack.MissingCaptions');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
