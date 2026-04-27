<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AriaAllowedAttrRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // run once per file
        if (0 !== $tokenIndex) {
            return;
        }

        $content = $this->getFullContent($tokens);

        foreach (RoleCatalog::getCatalog() as $role => $info) {
            if (preg_match_all('/role\s*=\s*(?:"|\')' . preg_quote($role, '/') . '(?:"|\')/i', $content, $m)) {
                // For simplicity, ensure at least one child-like token exists for roles that require children
                foreach ($info['required_children'] as $childRole) {
                    if (!preg_match('/role\s*=\s*(?:"|\')' . preg_quote($childRole, '/') . '(?:"|\')/i', $content)) {
                        $tokenRef = $tokens->get(0);
                        $emit(sprintf('Role "%s" should include children with role "%s".', $role, $childRole), $tokenRef, 'AriaRequired.ChildrenMissing');

                        // report once per role for determinism
                        break 2;
                    }
                }
            }
        }
    }
}
