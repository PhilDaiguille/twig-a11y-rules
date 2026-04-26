<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class FormLabelRule extends AbstractA11yRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<label')) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '>');

        // Check for for attribute
        if (preg_match('/\bfor\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $opening)) {
            // also ensure there's content (closing tag with some content)
            if (preg_match('/<label[^>]*>\s*[^<]+\s*<\/label>/i', $this->getFull($tokens))) {
                return;
            }
        }

        // If label wraps content and contains input/select/textarea
        if (preg_match('/<label[^>]*>\s*<input|<select|<textarea/i', $this->getFull($tokens))) {
            return;
        }

        $this->addError(
            '<label> must have a for attribute or non-empty content.',
            $token,
            'FormLabel.InvalidLabel'
        );
    }

    // collectUntil provided by parent

    private function getFull(Tokens $tokens): string
    {
        $s = '';
        foreach ($tokens->toArray() as $t) {
            $s .= $t->getValue();
        }

        return $s;
    }
}
