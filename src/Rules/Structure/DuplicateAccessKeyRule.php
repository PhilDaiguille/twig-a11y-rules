<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

/**
 * Detects duplicate accesskey attribute values in the same document.
 *
 * Duplicate accesskeys create unpredictable keyboard shortcuts and confuse
 * screen reader users who rely on them for navigation.
 *
 * WCAG 4.1.1 — Parsing.
 * axe-core: accesskeys
 */
final class DuplicateAccessKeyRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), 'accesskey')) {
            return;
        }

        if (!preg_match_all('/\baccesskey\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        $seen = [];
        foreach ($m as $match) {
            $doubleQuoted = $match[1] ?? '';
            $singleQuoted = $match[2] ?? '';
            $key = strtolower(trim('' !== $doubleQuoted ? $doubleQuoted : $singleQuoted));
            // Skip empty values and Twig expressions
            if ('' === $key) {
                continue;
            }

            if ($this->containsTwigExpressions($key)) {
                continue;
            }

            if (isset($seen[$key])) {
                $fakeToken = $tokens->get(0);
                $emit(
                    sprintf('Duplicate accesskey value "%s" found. Each accesskey must be unique within a page.', $key),
                    $fakeToken,
                    'DuplicateAccessKey.Duplicate'
                );

                return;
            }

            $seen[$key] = true;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
