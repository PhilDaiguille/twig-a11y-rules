<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AriaRequiredAttrRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains(strtolower($value), 'role=')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '/>/');

        if (preg_match('/role\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $tag, $m)) {
            $role = strtolower($m[1]);
            // minimal mapping of required attributes for demo purposes
            $required = [
                'img' => ['alt'],
                'link' => ['href'],
                'button' => [],
            ];

            if (isset($required[$role])) {
                foreach ($required[$role] as $attr) {
                    if (!preg_match('/\b'.preg_quote($attr, '/').'\s*=\s*(?:"|\')/i', $tag)) {
                        $this->addError(sprintf('Role "%s" requires attribute "%s".', $role, $attr), $token, 'AriaRequired.Missing');
                        break;
                    }
                }
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
