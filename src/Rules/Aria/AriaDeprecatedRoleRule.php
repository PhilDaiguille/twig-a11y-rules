<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaDeprecatedRoleRule extends AbstractA11yRule
{
    private const DEPRECATED = [
        'toolbar', // example deprecated role for tests
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);
        if (!preg_match_all('/role\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $full, $m)) {
            return;
        }

        $roles = array_map(strtolower(...), $m[1]);
        foreach ($roles as $role) {
            if (in_array($role, self::DEPRECATED, true)) {
                $token = $tokens->get(0);
                $emit(sprintf('ARIA role "%s" is deprecated.', $role), $token, 'AriaDeprecated.Deprecated');
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    protected function emitsWarnings(): bool
    {
        return true;
    }
}
