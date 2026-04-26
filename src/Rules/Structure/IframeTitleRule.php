<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class IframeTitleRule extends AbstractA11yRule
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

        $tag = $this->collectUntil($tokenIndex, $tokens, '>');
        if (!preg_match('/title\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $tag, $m) || '' === trim($m[1])) {
            // Keep the original message expected by tests (non-empty check retained)
            $this->addError('Iframe must have a title attribute.', $token, 'IframeTitle.Missing');
        }
    }

    // collectUntil provided by parent
}
