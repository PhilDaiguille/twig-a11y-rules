<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class EmptyTableHeaderRule extends AbstractA11yRule
{
    #[\Override]
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<th')) {
            return;
        }

        // Match <th ...>...</th> blocks and check whether the inner content
        // is empty (after stripping tags and Twig expressions).
        if (!preg_match_all('/<th\b[^>]*>(.*?)<\/th>/is', $full, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($m as $set) {
            $inner = $set[1][0];
            // Strip Twig expressions — a dynamic value counts as present.
            if (str_contains($inner, '{{')) {
                continue;
            }

            if (str_contains($inner, '{%')) {
                continue;
            }

            $text = trim(strip_tags($inner));
            if ('' === $text) {
                $emit(
                    'Table header <th> must not be empty.',
                    $this->tokenAtOffset($tokens, $set[0][1], $set[0][0]),
                    'Empty',
                );
            }
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
