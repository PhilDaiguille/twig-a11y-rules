<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ButtonContentRule extends AbstractRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<button')) {
            return;
        }

        $full = $this->collectUntil($tokenIndex, $tokens, '/<\/button>/i');

        // If inner text stripped from tags is empty and no aria-label attribute
        if (preg_match('/<button[^>]*>(.*?)<\/button>/is', $full, $m)) {
            $inner = $m[1];
            $textOnly = trim(strip_tags($inner));

            $opening = '';
            if (preg_match('/<button[^>]*>/i', $full, $o)) {
                $opening = $o[0];
            }

            if ('' === $textOnly && !preg_match('/\baria-label\s*=\s*("|\')/i', $opening)) {
                $emit('Button element without textual content must have an aria-label.', $token, 'ButtonContent.MissingContent');
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

    private function collectUntil(int $tokenIndex, Tokens $tokens, string $endPattern): string
    {
        $s = '';
        $i = $tokenIndex;
        $end = $tokenIndex + 200;
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
