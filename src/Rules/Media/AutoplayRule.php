<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AutoplayRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = strtolower($token->getValue());
        if (!str_contains($value, '<video') && !str_contains($value, '<audio')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '/>/');

        if (preg_match('/autoplay\b/i', $tag) && !preg_match('/\bmuted\b/i', $tag)) {
            $this->addError('Autoplaying media should be muted.', $token, 'Autoplay.NotMuted');
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
