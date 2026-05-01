<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaRequiredAttrRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        // Scan full file for role attributes to be robust against tokenization
        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), 'role=')) {
            return;
        }

        // Extended mapping of some roles to required attributes (non-exhaustive).
        // Each entry is a list of OR-groups: the role is valid when at least one attribute
        // in each group is present. A group with a single entry means that one attribute is mandatory.
        // A group with multiple entries means any one of them is sufficient.
        $requiredMap = [
            'img' => [['alt']],
            'link' => [['href']],
            'textbox' => [['aria-label', 'aria-labelledby']],  // either is acceptable
            'combobox' => [['aria-controls']],
            'button' => [],
            'checkbox' => [['aria-checked']],
            'radio' => [['aria-checked']],
        ];

        if (preg_match_all('/<([a-z0-9]+)([^>]*)>/i', $full, $tags, PREG_SET_ORDER)) {
            foreach ($tags as $set) {
                $attrs = $set[2];
                if (preg_match('/role\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $attrs, $m)) {
                    $role = strtolower($m[1]);
                    if (isset($requiredMap[$role])) {
                        foreach ($requiredMap[$role] as $group) {
                            // The group is satisfied when at least one of its attributes is present
                            $satisfied = false;
                            foreach ($group as $attr) {
                                if (preg_match('/\b'.preg_quote($attr, '/').'\s*=\s*(?:"|\')/i', $attrs)) {
                                    $satisfied = true;

                                    break;
                                }
                            }

                            if (!$satisfied) {
                                $tokenRef = $tokens->get(0);
                                $missing = implode('" or "', $group);
                                $emit(sprintf('Role "%s" requires attribute "%s".', $role, $missing), $tokenRef, 'AriaRequired.Missing');

                                // stop after first missing group found for test determinism
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
