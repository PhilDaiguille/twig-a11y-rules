<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaRequiredParentRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        $catalog = RoleCatalog::getCatalog();

        // Build inverse mapping childRole => parentRoles[]
        $inverse = [];
        foreach ($catalog as $role => $info) {
            foreach ($info['required_children'] as $child) {
                $inverse[$child][] = $role;
            }
        }

        if ([] === $inverse) {
            return;
        }

        // For each child role, find occurrences and verify a parent exists
        foreach ($inverse as $childRole => $parents) {
            if (!preg_match_all('/<([a-z0-9]+)[^>]*role\s*=\s*(?:"|\')'.preg_quote($childRole, '/').'(?:(?:"|\'))/i', $full, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($matches[0] as $occ) {
                $found = false;
                foreach ($parents as $parent) {
                    // Check for a parent wrapper containing this child occurrence
                    if (preg_match('/<([a-z0-9]+)[^>]*role\s*=\s*(?:"|\')'.preg_quote($parent, '/').'(?:(?:"|\'))[^>]*>.*?'.preg_quote($occ[0], '/').'.*?<\/\1>/is', $full)) {
                        $found = true;

                        break;
                    }
                }

                if (!$found) {
                    $token = $tokens->get(0);
                    $emit(sprintf('Role %s must be contained in one of: %s.', $childRole, implode(', ', $parents)), $token, 'AriaRequiredParent.MissingParent');
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
