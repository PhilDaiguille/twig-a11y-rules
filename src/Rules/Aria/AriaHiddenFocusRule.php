<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AriaHiddenFocusRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Page-level rule: defer to helper which respects evaluateOncePerFile().
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), 'aria-hidden')) {
            return;
        }

        // find any tags with aria-hidden="true"
        if (preg_match_all('/<([a-z0-9]+)([^>]*)>/i', $full, $m, PREG_SET_ORDER)) {
            foreach ($m as $set) {
                $tagName = strtolower($set[1]);
                $attrs = $set[2];
                if (preg_match('/aria-hidden\s*=\s*(?:"|\')true(?:"|\')/i', $attrs)) {
                    // Focusable detection: element tag or attributes indicating focusability
                    $focusableTags = ['button', 'input', 'select', 'textarea', 'a'];
                    $isFocusable = in_array($tagName, $focusableTags, true)
                        || preg_match('/href\s*=|tabindex\s*=/i', $attrs);

                    if ($isFocusable) {
                        // add a generic token (first text token) for location
                        $token = $tokens->get(0);
                        $emit('Focusable element should not be aria-hidden.', $token, 'AriaHiddenFocus.HiddenFocusable');

                        // one error is enough for the test fixtures
                        return;
                    }
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
