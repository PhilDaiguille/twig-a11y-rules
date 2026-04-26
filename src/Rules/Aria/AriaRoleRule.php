<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AriaRoleRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, 'role=')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '/>/');

        if (preg_match('/role\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $tag, $m)) {
            $role = strtolower(trim($m[1]));
            // minimal allowed roles list for static check
            $allowed = [
                'button', 'link', 'navigation', 'main', 'banner', 'contentinfo', 'complementary', 'form', 'search',
            ];
            if (!in_array($role, $allowed, true)) {
                $this->addError(
                    sprintf('Invalid ARIA role "%s".', $role),
                    $token,
                    'AriaRole.InvalidRole'
                );
            }
        }
    }

    private function collectUntil(int $tokenIndex, Tokens $tokens, string $endPattern): string
    {
        $s = '';
        $i = $tokenIndex;
        $end = $tokenIndex + 50;
        while ($i < $end) {
            $t = $tokens->get($i);
            $v = $t->getValue();
            $s .= $v;
            if (preg_match($endPattern, $s)) {
                break;
            }
            ++$i;
        }

        return $s;
    }
}
