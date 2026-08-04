<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Checks that every <label> element is well-formed: it must either have a
 * non-empty `for` attribute pointing to a visible field, or wrap a form
 * control directly.
 *
 * This rule targets the <label> element itself — the inverse of
 * AbstractFormFieldLabelRule which checks fields for their labels.
 * The two concerns are distinct and cannot share the same abstraction.
 */
final class FormLabelRule extends AbstractA11yRule
{
    private int $idx = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<label')) {
            return;
        }

        $opening = $this->collectOpeningTag($tokenIndex, $tokens, 'label');
        if ('' === $opening) {
            return;
        }

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

        ++$this->idx;
        $id = 'FormLabel.InvalidLabel';
        if ($this->idx > 1) {
            $id .= '#'.$this->idx;
        }

        $emit(
            '<label> must have a for attribute or non-empty content.',
            $token,
            $id
        );
    }

    protected function evaluateStart(Tokens $tokens): void
    {
        $this->idx = 0;
    }
}
