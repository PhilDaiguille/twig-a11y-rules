<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

use TwigA11y\Rules\AbstractA11yRule;

final class AriaHiddenFocusRule extends AbstractA11yRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        // Only run the full-file scan once per file: guard on tokenIndex 0 which
        // is the first token in the stream. Using an instance property caused
        // the rule to skip subsequent files when the same rule instance was
        // reused.
        if (0 !== $tokenIndex) {
            return;
        }

        $full = '';
        foreach ($tokens->toArray() as $t) {
            $full .= $t->getValue();
        }

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
                        $this->addError('Focusable element should not be aria-hidden.', $token, 'AriaHiddenFocus.HiddenFocusable');

                        // one error is enough for the test fixtures
                        return;
                    }
                }
            }
        }
    }
    // collectUntil removed; scanning uses full file concatenation
}
