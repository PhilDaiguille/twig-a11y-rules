<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class DuplicateIdRule extends AbstractA11yRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        // Only run once per file
        if (0 !== $tokenIndex) {
            return;
        }

        $full = '';
        foreach ($tokens->toArray() as $t) {
            $full .= $t->getValue();
        }

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

        foreach ($counts as $id => $cnt) {
            if ($cnt > 1) {
                // Report first token as location (use token 0)
                $token = $tokens->get(0);
                $this->addError(sprintf('Duplicate id "%s" found in document.', $id), $token, 'DuplicateId.Duplicate');
                // stop after first duplicate for determinism
                return;
            }
        }
    }
}
