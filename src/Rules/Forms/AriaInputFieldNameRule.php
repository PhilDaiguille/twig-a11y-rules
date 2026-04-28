<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaInputFieldNameRule extends AbstractA11yRule
{
    private const ROLES = [
        'textbox', 'combobox', 'searchbox', 'spinbutton',
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!preg_match_all('/<(div|span)[^>]*role\s*=\s*(?:"|\')([^"\']+)(?:"|\')[^>]*>/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        foreach ($m as $set) {
            $role = strtolower($set[2]);
            if (!in_array($role, self::ROLES, true)) {
                continue;
            }

            $opening = $set[0];
            // aria-label or aria-labelledby acceptable
            if (preg_match('/\baria-label\s*=\s*(?:"|\')/i', $opening)) {
                continue;
            }

            if (preg_match('/\baria-labelledby\s*=\s*(?:"|\')/i', $opening)) {
                continue;
            }

            // id + label[for] in full content
            if (preg_match('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $opening, $idm)) {
                $id = $idm[1];
                if (preg_match('/<label[^>]*for\s*=\s*(?:"|\')'.preg_quote($id, '/').'(?:(?:"|\'))/i', $full)) {
                    continue;
                }
            }

            $fakeToken = $tokens->get(0);
            $emit(sprintf('role="%s" element must have an accessible name (aria-label or aria-labelledby).', $role), $fakeToken, 'AriaInputFieldName.MissingName');
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
