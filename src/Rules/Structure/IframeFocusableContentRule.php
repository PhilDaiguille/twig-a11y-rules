<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class IframeFocusableContentRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), '<iframe')) {
            return;
        }

        // Find iframes with tabindex="-1"
        if (preg_match_all('/<iframe([^>]*)\btabindex\s*=\s*["\']-1["\'][^>]*>(.*?)<\/iframe>/is', $full, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $attrs = $m[1];
                $inner = $m[2];

                // If inner content contains focusable element, emit
                if (preg_match('/<(a|button|input|textarea|select|area)\b/i', $inner)) {
                    $pos = strpos($full, $m[0]);
                    $line = 1;
                    if (false !== $pos) {
                        $line += substr_count(substr($full, 0, $pos), "\n");
                    }

                    $fakeToken = $tokens->get(0);
                    $fakeToken = new Token(
                        $fakeToken->getType(),
                        $line,
                        1,
                        $fakeToken->getFilename(),
                        $m[0]
                    );

                    $emit('Iframe has tabindex="-1" but contains focusable content.', $fakeToken, 'Iframe.FocusableContent');

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
