<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ObjectAltRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = strtolower($token->getValue());
        if (!str_contains($value, '<object')) {
            return;
        }

        // Collect the full <object>...</object> block to check for fallback content
        $full = $this->collectUntil($tokenIndex, $tokens, '/<\/object>/i', 200);

        // Check inline attributes on the opening tag
        $tag = $this->collectUntil($tokenIndex, $tokens, '>');
        if (preg_match('/\btitle\s*=|\baria-label\s*=|\balt\s*=/i', $tag)) {
            return;
        }

        // Check <param name="alt"> inside element
        if (preg_match('/<param\s[^>]*name\s*=\s*["\']alt["\']/i', $full)) {
            return;
        }

        // Check for non-empty fallback text content between tags (axe-core: object-alt)
        if (preg_match('/<object[^>]*>(.*?)<\/object>/is', $full, $m)) {
            $fallback = trim(strip_tags($m[1]));
            if ('' !== $fallback) {
                return;
            }
        }

        $emit('Object element should have alternative text.', $token, 'ObjectAlt.Missing');
    }
}
