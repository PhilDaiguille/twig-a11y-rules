<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaRoleRule extends AbstractA11yRule
{
    #[\Override]
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $tag = $this->getFullContent($tokens);

        $m = [];
        if (!preg_match_all('/role\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $tag, $m, PREG_OFFSET_CAPTURE)) {
            return;
        }

        $allowed = RoleCatalog::getAllowedRoles();
        foreach ($m[1] as [$role, $offset]) {
            $role = strtolower($role);
            // Skip Twig dynamic expressions
            if ($this->containsTwigExpressions($role)) {
                continue;
            }

            if (!in_array($role, $allowed, true)) {
                $emit(
                    sprintf('Invalid ARIA role "%s".', $role),
                    $this->tokenAtOffset($tokens, $offset, $role),
                    'InvalidRole',
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
