<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class DuplicateIdRule extends AbstractA11yRule
{
    #[\Override]
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Only run once per file
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, 'id=')) {
            return;
        }

        // find all id attributes
        $m = [];
        if (!preg_match_all('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $full, $m, PREG_OFFSET_CAPTURE)) {
            return;
        }

        // Report every occurrence after the first, where the duplicate is.
        $seen = [];
        foreach ($m[1] as [$id, $offset]) {
            if (isset($seen[$id])) {
                $emit(
                    sprintf('Duplicate id "%s" found in document.', $id),
                    $this->tokenAtOffset($tokens, $offset, $id),
                    'Duplicate',
                );
            }

            $seen[$id] = true;
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
