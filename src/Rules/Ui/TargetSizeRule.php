<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Ui;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class TargetSizeRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = strtolower($this->getFullContent($tokens));

        // look for interactive elements with inline style width/height < 24px
        if (!preg_match_all('/<(a|button|input|div|span|label|i|svg)\b([^>]*)>/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        foreach ($m as $match) {
            $tag = $match[1];
            $attrs = $match[2];

            // cheap interactive detection: tag is a native interactive OR has role/buttonish attributes
            $isInteractive = in_array($tag, ['a', 'button', 'input'], true)
                || preg_match('/\bonclick\b/i', $attrs)
                || preg_match('/\brole=(?:"|\')?button(?:"|\')?/i', $attrs)
                || preg_match('/\btabindex\s*=\s*\d+/i', $attrs);

            if (!$isInteractive) {
                continue;
            }

            if (!preg_match('/style\s*=\s*["\']([^"\']+)["\']/i', $attrs, $sMatch)) {
                continue;
            }

            $style = $sMatch[1];
            // find width/height in px
            $small = false;
            if (preg_match_all('/(?:width|height)\s*:\s*(\d+)px/', $style, $sizeMatches, PREG_SET_ORDER)) {
                foreach ($sizeMatches as $sm) {
                    if ((int) $sm[1] < 24) {
                        $small = true;

                        break;
                    }
                }
            }

            if ($small) {
                $fake = $tokens->get(0);
                $emit('Interactive element has inline size < 24px; this may fail target-size (WCAG 2.5.8).', $fake, 'TargetSize.Small');

                return; // emit only once per file
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

}
