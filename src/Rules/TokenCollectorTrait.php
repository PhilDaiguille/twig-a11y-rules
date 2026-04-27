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
    protected function collectUntil(int $tokenIndex, Tokens $tokens, string $endPattern, int $limit = 200): string
    {
        $collected = '';
        $i = $tokenIndex;
        $end = $tokenIndex + $limit;

        while ($i <= $end && $tokens->has($i)) {
            $token = $tokens->get($i);
            $value = $token->getValue();
            $collected .= $value;

            if (str_starts_with($endPattern, '/')) {
                if ($this->safePregMatch($endPattern, $collected)) {
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

    /**
     * Safe wrapper around preg_match that avoids silencing errors with @ and
     * returns a boolean result. If the pattern is invalid, false is returned.
     */
    protected function safePregMatch(string $pattern, string $subject): bool
    {
        try {
            $res = preg_match($pattern, $subject);
        } catch (\Throwable $e) {
            return false;
        }

        if ($res === false) {
            return false;
        }

        return (bool) $res;
    }

    /**
     * Detect simple Twig expressions in a string ({{ ... }} or {% ... %}).
     */
    protected function containsTwigExpressions(string $s): bool
    {
        return (bool) preg_match('/\{\{.*?\}\}|\{%.+?%\}/s', $s);
    }
}
