<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

/**
 * Checks for <meta http-equiv="refresh"> with a non-zero timeout, which
 * causes automatic page redirects and violates WCAG 2.2.1 (Timing Adjustable).
 *
 * Axe-core rule: meta-refresh (Serious)
 * WCAG 2.2.1 — Timing Adjustable
 */
final class MetaRefreshRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!stripos($full, 'http-equiv')) {
            return;
        }

        // Find all <meta http-equiv="refresh" content="..."> tags
        if (!preg_match_all('/<meta[^>]+>/i', $full, $tags)) {
            return;
        }

        foreach ($tags[0] as $tag) {
            if (!preg_match('/\bhttp-equiv\s*=\s*(?:"|\')refresh(?:"|\')/i', $tag)) {
                continue;
            }

            // Extract the timeout value from content="N;url=..." or content="N"
            if (!preg_match('/\bcontent\s*=\s*(?:"|\')(\d+)/i', $tag, $m)) {
                continue;
            }

            $timeout = (int) $m[1];
            if ($timeout > 0) {
                $token = $tokens->get(0);
                $emit(
                    sprintf(
                        '<meta http-equiv="refresh"> with a non-zero timeout (%d) causes automatic page refresh (WCAG 2.2.1).',
                        $timeout
                    ),
                    $token,
                    'MetaRefresh.NonZeroTimeout'
                );
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
