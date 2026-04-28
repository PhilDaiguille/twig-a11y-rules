<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class InputTypeRule extends AbstractA11yRule
{
    /**
     * Input types that require an autocomplete attribute for WCAG 1.3.5 (Identify Input Purpose).
     */
    private const AUTOCOMPLETE_REQUIRED_TYPES = ['email', 'tel', 'name', 'username', 'new-password', 'current-password'];

    /**
     * Check inputs with personal-data types have an autocomplete attribute (WCAG 1.3.5).
     */
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<input')) {
            return;
        }

        $typePattern = implode('|', array_map(preg_quote(...), self::AUTOCOMPLETE_REQUIRED_TYPES));

        if (!preg_match_all('/<input\b([^>]*\btype\s*=\s*(?:"|\')(?:'.$typePattern.')(?:"|\')[^>]*)>/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        foreach ($m as $set) {
            $attrs = $set[1];
            if (!preg_match('/\bautocomplete\b\s*=\s*(?:"|\')/i', $attrs)) {
                // Extract matched type for a more precise error message
                preg_match('/\btype\s*=\s*(?:"|\')([^"\']+)(?:"|\')/', $attrs, $tm);
                $type = $tm[1] ?? 'unknown';

                $token = $tokens->get(0);
                $emit(sprintf('Input of type "%s" should include an autocomplete attribute (WCAG 1.3.5).', $type), $token, 'InputType.MissingAutocomplete');

                return;
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
