<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class LangAttributeRule extends AbstractRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<html')) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '/>/');

        if (!preg_match('/\blang\s*=\s*("|\')/i', $opening)) {
            $emit('The <html> element should have a lang attribute.', $token, 'LangAttribute.MissingLang');
        }
    }

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $emit = function (string $message, Token $token, ?string $id = null): void {
            $this->addError($message, $token, $id);
        };

        $this->evaluate($tokens, $tokenIndex, $emit);
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
