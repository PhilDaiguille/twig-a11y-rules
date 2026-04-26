<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class VideoTrackRule extends AbstractA11yRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        // Only run once per file
        if (0 !== $tokenIndex) {
            return;
        }

        $full = '';
        foreach ($tokens->toArray() as $t) {
            $full .= $t->getValue();
        }

        if (!str_contains($full, '<video')) {
            return;
        }

        // Find all <video ...>...</video> blocks
        if (!preg_match_all('/<video\b([^>]*)>(.*?)<\/video>/is', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        foreach ($m as $set) {
            $openAttrs = $set[1];
            $content = $set[2];

            // If a <track kind="captions" exists inside the video block, OK
            if (preg_match('/<track\b[^>]*\bkind\s*=\s*(?:"|\')captions(?:"|\')/i', $content)) {
                continue;
            }

            // No captions found — report error at token 0 for determinism
            $token = $tokens->get(0);
            $this->addError('Video should have captions (track kind="captions").', $token, 'VideoTrack.MissingCaptions');
            return;
        }
    }
}
