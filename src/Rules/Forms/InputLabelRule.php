<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class InputLabelRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<input')) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '/>', 50);

        // Has aria-label? then OK
        if (preg_match('/\baria-label\s*=\s*("|\')/i', $opening)) {
            return;
        }

        // Id present?
        $id = null;
        if (preg_match('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $opening, $m)) {
            $id = $m[1];
        }

        // Build full template to search for label[for]
        // Concatenate all token values to search for <label for="id"> or wrapping <label>
        $full = '';
        foreach ($tokens->toArray() as $t) {
            $full .= $t->getValue();
        }

        if (null !== $id) {
            if (preg_match('/<label[^>]*for\s*=\s*["\']'.preg_quote($id, '/').'["\']/i', $full)) {
                return;
            }
        }

        // Check for wrapping label: any <label ...> that contains an <input
        if (preg_match('/<label[^>]*>.*?<input[^>]*>/is', $full)) {
            return;
        }

        $this->addError(
            'Input element must have an associated <label> or an aria-label.',
            $token,
            'InputLabel.MissingLabel'
        );
    }

    // collectUntil provided by parent
}
