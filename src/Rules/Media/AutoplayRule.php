<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AutoplayRule extends AbstractA11yRule
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

        $tag = $this->collectUntil($tokenIndex, $tokens, '/>');

        if (preg_match('/autoplay\b/i', $tag) && !preg_match('/\bmuted\b/i', $tag)) {
            $this->addError('Autoplaying media should be muted.', $token, 'Autoplay.NotMuted');
        }
    }

    // collectUntil provided by parent
}
