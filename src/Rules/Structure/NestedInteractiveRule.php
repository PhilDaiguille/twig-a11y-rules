<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

/**
 * Detects interactive elements nested inside other interactive elements.
 *
 * Common violations:
 *   - <button> or <input> inside <a>
 *   - <a> inside <button>
 *
 * These patterns produce invalid HTML and confusing behavior for AT users.
 *
 * WCAG 4.1.1 — Parsing.
 * axe-core: nested-interactive
 */
final class NestedInteractiveRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        // <button>, <input> or <select> nested inside <a>
        if (preg_match_all('/<a\b[^>]*>(.*?)<\/a>/is', $full, $anchors, PREG_SET_ORDER)) {
            foreach ($anchors as $anchor) {
                $inner = $anchor[1];
                if (preg_match('/<(button|input|select|textarea)\b/i', $inner, $nested)) {
                    $fakeToken = $tokens->get(0);
                    $emit(
                        sprintf('Interactive element <%s> must not be nested inside an <a>.', strtolower($nested[1])),
                        $fakeToken,
                        'NestedInteractive.InsideAnchor'
                    );

                    return;
                }
            }
        }

        // <a> nested inside <button>
        if (preg_match_all('/<button\b[^>]*>(.*?)<\/button>/is', $full, $buttons, PREG_SET_ORDER)) {
            foreach ($buttons as $button) {
                $inner = $button[1];
                if (preg_match('/<a\b/i', $inner)) {
                    $fakeToken = $tokens->get(0);
                    $emit(
                        'Interactive element <a> must not be nested inside a <button>.',
                        $fakeToken,
                        'NestedInteractive.InsideButton'
                    );

                    return;
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
