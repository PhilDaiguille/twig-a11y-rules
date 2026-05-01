<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class EmptyTableHeaderRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<th')) {
            return;
        }

        // Match <th ...>...</th> blocks and check whether the inner content
        // is empty (after stripping tags and Twig expressions).
        if (!preg_match_all('/<th\b[^>]*>(.*?)<\/th>/is', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        $idx = 0;
        foreach ($m as $set) {
            $inner = $set[1];
            // Strip Twig expressions — a dynamic value counts as present.
            if (str_contains($inner, '{{')) {
                continue;
            }

            if (str_contains($inner, '{%')) {
                continue;
            }

            $text = trim(strip_tags($inner));
            if ('' === $text) {
                ++$idx;
                $id = 'EmptyTableHeader.Empty';
                if ($idx > 1) {
                    $id .= '#'.$idx;
                }

                $emit('Table header <th> must not be empty.', $tokens->get(0), $id);
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
