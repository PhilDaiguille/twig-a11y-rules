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
            if (preg_match($endPattern, $s)) {
                break;
            }
            ++$i;
        }

        return $s;
    }
}
