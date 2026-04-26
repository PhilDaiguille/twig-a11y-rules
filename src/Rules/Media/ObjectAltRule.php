<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ObjectAltRule extends AbstractA11yRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = strtolower($token->getValue());
        if (!str_contains($value, '<object')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '>');
        if (!preg_match('/\btitle\s*=|\baria-label\s*=|\balt\s*=|>.*?<param\s+name="alt"/i', $tag)) {
            $this->addError('Object element should have alternative text.', $token, 'ObjectAlt.Missing');
        }
    }

    // collectUntil provided by parent
}
