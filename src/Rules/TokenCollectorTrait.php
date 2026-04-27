<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigCsFixer\Token\Tokens;

trait TokenCollectorTrait
{
    /**
     * Collect token values starting at $tokenIndex until $endPattern matches or
     * $limit tokens have been consumed. $limit is treated as an offset from the
     * starting index (default 50 tokens).
     */
    protected function collectUntil(int $tokenIndex, Tokens $tokens, string $endPattern, int $limit = 50): string
    {
        $collected = '';
        $i = $tokenIndex;
        $end = $tokenIndex + $limit;

        while ($i <= $end && $tokens->has($i)) {
            $token = $tokens->get($i);
            $value = $token->getValue();
            $collected .= $value;

            if (str_starts_with($endPattern, '/')) {
                if (@preg_match($endPattern, $collected)) {
                    break;
                }
            } elseif (str_contains($collected, $endPattern)) {
                break;
            }

            ++$i;
        }

        return $collected;
    }

    protected function collectTag(int $tokenIndex, Tokens $tokens, int $limit = 50): string
    {
        return $this->collectUntil($tokenIndex, $tokens, '>', $limit);
    }
}
