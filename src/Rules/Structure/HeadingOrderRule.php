<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class HeadingOrderRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Page-level rule: defer to evaluateOncePerFile via shouldSkipByTokenIndex
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $token = $tokens->get($tokenIndex);

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<h')) {
            return;
        }

        // Use PREG_SET_ORDER to get predictable structure and avoid PHPStan type issues
        $levels = [];
        if (preg_match_all('/<h([1-6])[^>]*>/i', $full, $m, PREG_SET_ORDER)) {
            foreach ($m as $set) {
                $levels[] = (int) $set[1];
            }
        }

        $prev = 0;
        $errorIndex = 0;
        foreach ($levels as $lvl) {
            if (0 !== $prev && $lvl > $prev + 1) {
                ++$errorIndex;
                $id = 'HeadingOrder.Invalid';
                if ($errorIndex > 1) {
                    // append an index so each violation identifier is unique for tests
                    $id .= '#'.$errorIndex;
                }

                $emit(
                    sprintf('Heading level jumped from h%d to h%d.', $prev, $lvl),
                    $token,
                    $id
                );

                // continue scanning to collect all jumps in the file
            }

            $prev = $lvl;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    // No template kind restriction: headings can appear in fragments and
    // full-page templates alike.
}
