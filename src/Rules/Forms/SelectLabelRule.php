<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class SelectLabelRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<select')) {
            return;
        }

        // Collect opening tag
        $opening = $this->collectUntil($tokenIndex, $tokens, '>');

        $id = null;
        if (preg_match('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $opening, $m)) {
            $id = $m[1];
        }

        // If the select has an aria-labelledby attribute, consider it labelled.
        // We accept presence here; a dedicated rule should validate referenced IDs.
        if (preg_match('/\baria-labelledby\s*=\s*(?:"([^"]+)"|\'([^\']+)\')/i', $opening, $mm)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (null !== $id && preg_match('/<label[^>]*for\s*=\s*["\']'.preg_quote($id, '/').'["\']/i', $full)) {
            return;
        }

        // Label wrapping the select (implicit association)
        if (preg_match('/<label[^>]*>\s*<select[^>]*>/i', $full)) {
            return;
        }

        // Also accept aria-label directly on the select
        if (preg_match('/<select[^>]*\baria-label\s*=\s*(?:"([^"]+)"|\'([^\']+)\')/i', $opening)) {
            return;
        }

        $emit('Select element must have an associated <label>.', $token, 'SelectLabel.Missing');
    }
}
