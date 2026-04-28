<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class FormLabelRule extends AbstractA11yRule
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
        if (!str_contains($value, '<label')) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '>');
        $full = $this->getFullContent($tokens);

        // Check for for attribute + ensure that the referenced <label for="id"> has non-empty content
        $forId = '';
        if (preg_match('/\bfor\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $opening, $m)) {
            $forId = $m[1];
        }

        if ('' !== $forId && preg_match('/<label[^>]*for\s*=\s*["\']'.preg_quote($forId, '/').'["\'][^>]*>\s*[^<]+\s*<\/label>/i', $full)) {
            return;
        }

        // If label wraps content and contains input/select/textarea
        if (preg_match('/<label[^>]*>\s*(?:<input|<select|<textarea)/i', $full)) {
            return;
        }

        $emit(
            '<label> must have a for attribute or non-empty content.',
            $token,
            'FormLabel.InvalidLabel'
        );
    }
}
