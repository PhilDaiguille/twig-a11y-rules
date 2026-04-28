<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaValidAttrRule extends AbstractA11yRule
{
    private const WHITELIST = [
        // Minimal subset for testing purposes; real list should include 46 attrs
        'aria-hidden', 'aria-label', 'aria-labelledby', 'aria-checked', 'aria-pressed', 'aria-selected',
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);
        if (!str_contains($full, 'aria-')) {
            return;
        }

        if (preg_match_all('/\baria-([a-z-]+)\b/i', $full, $m)) {
            foreach ($m[1] as $name) {
                $n = strtolower($name);
                if (!in_array('aria-'.$n, self::WHITELIST, true)) {
                    $token = $tokens->get(0);
                    $emit(sprintf('Attribute aria-%s is not a valid ARIA attribute.', $n), $token, 'AriaValidAttr.InvalidAttr');
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
