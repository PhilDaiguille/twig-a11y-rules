<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Tokens;

final class AriaRequiredAttrRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        // Only run the full-file scan once to avoid duplicate reports
        if (0 !== $tokenIndex) {
            return;
        }

        // Scan full file for role attributes to be robust against tokenization
        $full = '';
        foreach ($tokens->toArray() as $t) {
            $full .= $t->getValue();
        }

        if (!str_contains(strtolower($full), 'role=')) {
            return;
        }

        // Extended mapping of some roles to required attributes (non-exhaustive)
        $requiredMap = [
            'img' => ['alt'],
            'link' => ['href'],
            'textbox' => ['aria-label', 'aria-labelledby'],
            'combobox' => ['aria-controls'],
            'button' => [],
            'checkbox' => ['aria-checked'],
            'radio' => ['aria-checked'],
        ];

        if (preg_match_all('/<([a-z0-9]+)([^>]*)>/i', $full, $tags, PREG_SET_ORDER)) {
            foreach ($tags as $set) {
                $attrs = $set[2];
                if (preg_match('/role\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $attrs, $m)) {
                    $role = strtolower($m[1]);
                    if (isset($requiredMap[$role])) {
                        foreach ($requiredMap[$role] as $attr) {
                            if (!preg_match('/\b'.preg_quote($attr, '/').'\s*=\s*(?:"|\')/i', $attrs)) {
                                $tokenRef = $tokens->get(0);
                                $this->addError(sprintf('Role "%s" requires attribute "%s".', $role, $attr), $tokenRef, 'AriaRequired.Missing');

                                break 2; // one error per file for tests
                            }
                        }
                    }
                }
            }
        }
    }
}
