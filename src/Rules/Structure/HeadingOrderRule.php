<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class HeadingOrderRule extends AbstractA11yRule
{
    #[\Override]
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<h')) {
            return;
        }

        $m = [];
        if (!preg_match_all('/<h([1-6])[^>]*>/i', $full, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        $prev = 0;
        foreach ($m as $set) {
            $lvl = (int) $set[1][0];
            if (0 !== $prev && $lvl > ($prev + 1)) {
                $emit(
                    sprintf('Heading level jumped from h%d to h%d.', $prev, $lvl),
                    $this->tokenAtOffset($tokens, $set[0][1], $set[0][0]),
                    'Invalid',
                );
            }

            $prev = $lvl;
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    // No template kind restriction: headings can appear in fragments and
    // full-page templates alike.
}
