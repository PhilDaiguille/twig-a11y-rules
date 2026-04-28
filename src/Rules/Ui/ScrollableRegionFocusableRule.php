<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Ui;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class ScrollableRegionFocusableRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 !== $tokenIndex) {
            return;
        }

        $full = $this->getFullContent($tokens);
        if (false === stripos($full, 'overflow:')) {
            return;
        }

        if (preg_match_all('/style\s*=\s*["\']([^"\']*overflow\s*:\s*(scroll|auto)[^"\']*)["\']/i', $full, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                // if no tabindex on the element, flag
                if (!preg_match('/tabindex\s*=\s*["\'][0-9-]+["\']/', $match[0])) {
                    $fakeToken = $tokens->get(0);
                    $emit('Scrollable region with overflow must be keyboard-focusable via tabindex.', $fakeToken, 'Scrollable.Focusable');

                    return;
                }
            }
        }
    }
}
