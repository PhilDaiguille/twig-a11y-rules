<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaAllowedAttrRule extends AbstractA11yRule
{
    // Simplified allowed attrs per role (not exhaustive)
    /**
     * @var array<string, string[]>
     */
    private array $allowed = [
        'button' => ['aria-pressed', 'aria-label', 'aria-labelledby'],
        'textbox' => ['aria-label', 'aria-labelledby', 'aria-required'],
        'img' => ['aria-label', 'aria-labelledby'],
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = strtolower($this->getFullContent($tokens));
        if (!str_contains($full, 'aria-')) {
            return;
        }

        if (preg_match_all('/<([a-z0-9]+)([^>]*)\srole\s*=\s*(?:"|\')([^"\']+)(?:"|\')([^>]*)>/i', $full, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $role = strtolower($m[3]);
                $attrs = $m[2].' '.$m[4];
                if (!isset($this->allowed[$role])) {
                    continue;
                }

                foreach ($this->allowed[$role] as $attr) {
                    // nothing
                }

                // naive check: find any aria- attribute not in allowed list
                if (preg_match_all('/\baria-[a-z0-9-]+\s*=\s*(?:"|\')[^"\']*(?:"|\')/i', $attrs, $am)) {
                    foreach ($am[0] as $ariaRaw) {
                        if (preg_match('/\baria-([a-z0-9-]+)/i', $ariaRaw, $an)) {
                            $name = strtolower($an[1]);
                            if (!in_array('aria-'.$name, $this->allowed[$role], true)) {
                                $fakeToken = $tokens->get(0);
                                $emit(sprintf('Attribute aria-%s is not allowed on role %s.', $name, $role), $fakeToken, 'AriaAllowed.Invalid');

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
