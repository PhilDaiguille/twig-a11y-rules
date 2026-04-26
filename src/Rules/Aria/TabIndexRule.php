<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class TabIndexRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, 'tabindex')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '/>/');

        if (preg_match('/tabindex\s*=\s*(?:"|\')?([\-0-9]+)(?:"|\')?/i', $tag, $m)) {
            $num = (int) $m[1];
            if ($num > 0) {
                $this->addError(
                    'Avoid positive tabindex values — use 0 or manage focus order differently.',
                    $token,
                    'TabIndex.PositiveTabindex'
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
