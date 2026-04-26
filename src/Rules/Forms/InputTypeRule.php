<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class InputTypeRule extends AbstractA11yRule
{
    /**
     * Check inputs with certain types have an autocomplete attribute.
     */
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        if (0 !== $tokenIndex) {
            return;
        }

        $full = '';
        foreach ($tokens->toArray() as $t) {
            $full .= $t->getValue();
        }

        // quick bail
        if (!str_contains($full, '<input')) {
            return;
        }

        // find inputs of type email/tel/name etc - we'll check only email for now
        if (!preg_match_all('/<input\b([^>]*\btype\s*=\s*(?:"|\')email(?:"|\')[^>]*)>/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        foreach ($m as $set) {
            $attrs = $set[1];
            if (!preg_match('/\bautocomplete\b\s*=\s*(?:"|\')/i', $attrs)) {
                $token = $tokens->get(0);
                $this->addError('Input of type "email" should include an autocomplete attribute.', $token, 'InputType.MissingAutocomplete');

                return;
            }
        }
    }
}
