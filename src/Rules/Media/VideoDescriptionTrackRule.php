<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Media\VideoDescriptionTrackRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 1.2.5 AA — <video> elements must include an audio description track
 * (<track kind="descriptions">) so that blind users can follow visual-only content.
 *
 * @see VideoDescriptionTrackRuleTest
 * @see VideoDescriptionTrackRuleTest
 * @see VideoDescriptionTrackRuleTest
 * @see VideoDescriptionTrackRuleTest
 * @see VideoDescriptionTrackRuleTest
 * @see VideoDescriptionTrackRuleTest
 */
final class VideoDescriptionTrackRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<video')) {
            return;
        }

        if (!preg_match_all('/<video\b([^>]*)>(.*?)<\/video>/is', $full, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $set) {
            $content = $set[2];

            if (preg_match('/<track\b[^>]*\bkind\s*=\s*(?:"|\')descriptions(?:"|\')/i', $content)) {
                continue;
            }

            $emit(
                'Video should have an audio description track (<track kind="descriptions">) (WCAG 1.2.5).',
                $tokens->get(0),
                'MissingDescriptions'
            );

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
