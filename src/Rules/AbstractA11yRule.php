<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Tokens;

abstract class AbstractA11yRule extends AbstractRule
{
    /**
     * Collect token values starting at $tokenIndex until $endPattern matches or
     * $limit tokens have been consumed. $limit is treated as an offset from the
     * starting index (default 50 tokens).
     */
    protected function collectUntil(int $tokenIndex, Tokens $tokens, string $endPattern, int $limit = 50): string
    {
        $s = '';
        $i = $tokenIndex;
        $end = $tokenIndex + $limit;
        while ($i <= $end && $tokens->has($i)) {
            $t = $tokens->get($i);
            $v = $t->getValue();
            $s .= $v;
            // Support both regular-expression patterns (e.g. '/<\/a>/i') and
            // simple literal terminators (e.g. '>'). If the provided
            // $endPattern starts with a '/' we treat it as a regex and call
            // preg_match; otherwise use a fast string search.
            if (str_starts_with($endPattern, '/')) {
                if (@preg_match($endPattern, $s)) {
                    break;
                }
            } else {
                if (str_contains($s, $endPattern)) {
                    break;
                }
            }

            ++$i;
        }

        return $s;
    }
}
