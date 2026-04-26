<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AnchorContentRule extends AbstractRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<a')) {
            return;
        }

        $full = $this->collectUntil($tokenIndex, $tokens, '/<\/a>/i');

        if (preg_match('/<a[^>]*>(.*?)<\/a>/is', $full, $m)) {
            $inner = $m[1];
            $textOnly = trim(strip_tags($inner));

            $opening = '';
            if (preg_match('/<a[^>]*>/i', $full, $o)) {
                $opening = $o[0];
            }

            if ('' === $textOnly
                && !preg_match('/\baria-label\s*=\s*("|\')/i', $opening)
                && !preg_match('/\btitle\s*=\s*("|\')/i', $opening)
            ) {
                // Axe-core rule reference: link-name
                $emit('Anchor element without accessible name (axe-core: link-name) should have an aria-label or title.', $token, 'AnchorContent.Warning.LinkName');
            }
        }
    }

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $emit = function (string $message, Token $token, ?string $id = null): void {
            // preserve warning semantics
            if (null === $id) {
                $this->addWarning($message, $token);
            } else {
                $this->addWarning($message, $token, $id);
            }
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
