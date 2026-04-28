<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaRoleRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $tag = $this->getFullContent($tokens);

        if (!preg_match_all('/role\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $tag, $m)) {
            return;
        }

        $allowed = RoleCatalog::getAllowedRoles();
        $roles = array_map(strtolower(...), $m[1]);

        $invalid = [];
        foreach ($roles as $role) {
            // Skip Twig dynamic expressions
            if ($this->containsTwigExpressions($role)) {
                continue;
            }

            if (!in_array($role, $allowed, true)) {
                $invalid[] = $role;
            }
        }

        if ([] === $invalid) {
            return;
        }

        $tokenRef = $tokens->get(0);
        $idx = 0;
        foreach ($invalid as $role) {
            ++$idx;
            $id = 'AriaRole.InvalidRole';
            if ($idx > 1) {
                $id .= '#'.$idx;
            }

            $emit(sprintf('Invalid ARIA role "%s".', $role), $tokenRef, $id);
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
