<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Checks that <input type="image"> elements have a non-empty alt attribute.
 *
 * Axe-core rule: input-image-alt (Critical)
 * WCAG 1.1.1 — Non-text Content
 */
final class InputImageAltRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains(strtolower($value), '<input')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '>');

        // Only target type="image"
        if (!preg_match('/\btype\s*=\s*(?:"|\')image(?:"|\')/i', $tag)) {
            return;
        }

        // Must have a non-empty alt attribute (or aria-label as fallback)
        if (preg_match('/\balt\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $tag, $m) && '' !== trim($m[1])) {
            return;
        }

        if (preg_match('/\baria-label\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $tag, $m) && '' !== trim($m[1])) {
            return;
        }

        $emit('<input type="image"> must have a non-empty alt attribute.', $token, 'InputImageAlt.Missing');
    }
}
