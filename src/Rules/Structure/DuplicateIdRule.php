<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class DuplicateIdRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Only run once per file
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, 'id=')) {
            return;
        }

        // find all id attributes
        if (!preg_match_all('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $full, $m)) {
            return;
        }

        $ids = $m[1];
        $counts = [];
        foreach ($ids as $id) {
            $counts[$id] = ($counts[$id] ?? 0) + 1;
        }

        $token = $tokens->get(0);
        $idx = 0;
        foreach ($counts as $id => $cnt) {
            if ($cnt > 1) {
                ++$idx;
                $ident = 'DuplicateId.Duplicate';
                if ($idx > 1) {
                    $ident .= '#'.$idx;
                }

                $emit(sprintf('Duplicate id "%s" found in document.', $id), $token, $ident);
                // continue to report other duplicates
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
