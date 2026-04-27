<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class InputLabelRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<input')) {
            return;
        }

        // Use a simple literal terminator to avoid malformed regex patterns
        $opening = $this->collectUntil($tokenIndex, $tokens, '>', 50);

        // Has aria-label? then OK
        if (preg_match('/\baria-label\s*=\s*("|\')/i', $opening)) {
            return;
        }

        // Ignore hidden inputs: they don't require accessible labels
        if (preg_match('/\btype\s*=\s*["\']hidden["\']/i', $opening)) {
            return;
        }

        // Id present?
        $id = null;
        if (preg_match('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $opening, $m)) {
            $id = $m[1];
        }

        $full = $this->getFullContent($tokens);

        if (null !== $id && preg_match('/<label[^>]*for\s*=\s*["\']'.preg_quote($id, '/').'["\']/i', $full)) {
            return;
        }

        // Check for wrapping label: any <label ...> that contains an <input
        if (preg_match('/<label[^>]*>.*?<input[^>]*>/is', $full)) {
            return;
        }

        $emit(
            'Input element must have an associated <label> or an aria-label.',
            $token,
            'InputLabel.MissingLabel'
        );
    }
}
