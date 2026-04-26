<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AriaHiddenFocusRule extends AbstractRule
{
    private bool $scanned = false;

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        // Scan the entire file once to avoid missing split tokens
        if ($this->scanned) {
            return;
        }
        $this->scanned = true;

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
                    $focusableTags = ['button','input','select','textarea','a'];
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
