<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class TabIndexRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, 'tabindex')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '/>', 50);

        if (preg_match('/tabindex\s*=\s*(?:"|\')?([\-0-9]+)(?:"|\')?/i', $tag, $m)) {
            $num = (int) $m[1];
            if ($num > 0) {
                $emit('Avoid positive tabindex values — use 0 or manage focus order differently.', $token, 'TabIndex.PositiveTabindex');
            }
        }
    }

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $emit = function (string $message, Token $token, ?string $id = null): void {
            $this->addError($message, $token, $id);
        };

        $this->evaluate($tokens, $tokenIndex, $emit);
    }

    // collectUntil provided by parent
}
