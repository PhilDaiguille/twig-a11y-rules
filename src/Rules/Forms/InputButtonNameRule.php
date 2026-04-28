<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class InputButtonNameRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<input')) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '>', 200);

        // Only look for submit/button inputs
        if (!preg_match('/\btype\s*=\s*["\'](submit|button)["\']/i', $opening)) {
            return;
        }

        // If value or aria-label present, it's okay
        if (preg_match('/\bvalue\s*=\s*["\'][^"\']+["\']/', $opening)) {
            return;
        }

        if (preg_match('/\baria-label\s*=\s*["\'][^"\']+["\']/', $opening)) {
            return;
        }

        $emit('Submit/button input must have a visible name via value or aria-label.', $token, 'InputButton.MissingName');
    }
}
