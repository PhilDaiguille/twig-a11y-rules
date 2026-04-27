<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ImgAltRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<img')) {
            return;
        }

        $fullTag = $this->collectTag($tokenIndex, $tokens);

        if (!str_contains($fullTag, '>')) {
            return;
        }

        if (!preg_match('/\balt\s*=/i', $fullTag)) {
            $emit('Missing alt attribute on <img> tag.', $token, 'ImgAlt.MissingAlt');

            return;
        }

        if (preg_match('/\balt\s*=\s*(["\'])\1/i', $fullTag)) {
            $hasDecorativeRole = preg_match(
                '/\brole\s*=\s*(["\'])(?:presentation|none)\1/i',
                $fullTag
            );
            if (!$hasDecorativeRole) {
                $emit('Empty alt on <img> requires role="presentation" or role="none".', $token, 'ImgAlt.EmptyAlt');
            }
        }
    }
}
