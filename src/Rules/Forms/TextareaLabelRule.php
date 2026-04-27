<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class TextareaLabelRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<textarea')) {
            return;
        }

        // Collect opening tag
        $opening = $this->collectUntil($tokenIndex, $tokens, '>');

        $id = null;
        if (preg_match('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $opening, $m)) {
            $id = $m[1];
        }

        $full = $this->getFullContent($tokens);

        if (null !== $id) {
            if (preg_match('/<label[^>]*for\s*=\s*["\']'.preg_quote($id, '/').'["\']/i', $full)) {
                return;
            }
        }

        if (preg_match('/<label[^>]*>\s*<textarea[^>]*>/i', $full)) {
            return;
        }

        $emit('Textarea must have an associated <label>.', $token, 'TextareaLabel.Missing');
    }
}
