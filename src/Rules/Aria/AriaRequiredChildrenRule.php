<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaRequiredChildrenRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        $catalog = RoleCatalog::getCatalog();

        foreach ($catalog as $role => $info) {
            if (empty($info['required_children'])) {
                continue;
            }

            // Find elements with this role and inspect their inner HTML for required child roles
            if (!preg_match_all('/<([a-z0-9]+)[^>]*role\s*=\s*(?:"|\')'.preg_quote($role, '/').'(?:(?:"|\')[^>]*)>(.*?)<\/\1>/is', $full, $m, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($m as $set) {
                // PREG_SET_ORDER with the regex above guarantees group 2 exists
                // for each match, so access it directly.
                $inner = $set[2];
                $found = false;
                foreach ($info['required_children'] as $childRole) {
                    if (preg_match('/role\s*=\s*(?:"|\')'.preg_quote($childRole, '/').'(?:(?:"|\'))/i', $inner)) {
                        $found = true;

                        break;
                    }
                }

                if (!$found) {
                    $token = $tokens->get(0);
                    $emit(sprintf('Role %s must contain at least one of: %s.', $role, implode(', ', $info['required_children'])), $token, 'AriaRequiredChildren.MissingChild');
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
