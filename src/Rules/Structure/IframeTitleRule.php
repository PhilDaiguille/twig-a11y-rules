<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class IframeTitleRule extends AbstractA11yRule
{
    private int $idx = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 === $tokenIndex) {
            $this->idx = 0;
        }

        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains(strtolower($value), '<iframe')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '>');

        // Title attribute must be present and non-empty
        if (!preg_match('/title\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $tag, $m) || '' === trim($m[1])) {
            ++$this->idx;
            $id = 'IframeTitle.Missing';
            if ($this->idx > 1) {
                $id .= '#'.$this->idx;
            }

            $emit('Iframe must have a non-empty title attribute.', $token, $id);
        }
    }
}
