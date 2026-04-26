<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class IframeTitleRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains(strtolower($value), '<iframe')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '/>/');
        if (!preg_match('/title\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $tag)) {
            $this->addError('Iframe must have a title attribute.', $token, 'IframeTitle.Missing');
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
