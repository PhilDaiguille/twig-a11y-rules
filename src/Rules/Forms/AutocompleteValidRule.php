<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AutocompleteValidRule extends AbstractA11yRule
{
    /**
     * Official-ish list (not exhaustive) for common fields
     * Source: WCAG / HTML spec.
     *
     * @var string[]
     */
    private array $allowed = [
        'name', 'honorific-prefix', 'given-name', 'additional-name', 'family-name', 'honorific-suffix',
        'nickname', 'username', 'new-password', 'current-password', 'organization-title', 'organization',
        'street-address', 'address-line1', 'address-line2', 'address-line3', 'address-level4', 'address-level3',
        'address-level2', 'address-level1', 'country', 'country-name', 'postal-code', 'email', 'tel', 'bday',
        'cc-number', 'cc-exp', 'cc-exp-month', 'cc-exp-year', 'cc-csc', 'off',
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 !== $tokenIndex) {
            return;
        }

        $full = strtolower($this->getFullContent($tokens));

        if (!str_contains($full, 'autocomplete')) {
            return;
        }

        if (preg_match_all('/autocomplete\s*=\s*["\']([^"\']+)["\']/i', $full, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                $value = trim($match[1]);
                // space-separated tokens allowed
                $parts = preg_split('/\s+/', $value);
                if (false === $parts) {
                    $parts = [];
                }

                foreach ($parts as $token) {
                    if ('' === $token) {
                        continue;
                    }

                    if (!in_array($token, $this->allowed, true)) {
                        $fakeToken = $tokens->get(0);
                        $emit(sprintf('Invalid autocomplete value "%s".', $token), $fakeToken, 'Autocomplete.Invalid');

                        return;
                    }
                }
            }
        }
    }
}
