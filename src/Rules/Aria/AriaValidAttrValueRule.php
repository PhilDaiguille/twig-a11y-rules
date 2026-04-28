<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaValidAttrValueRule extends AbstractA11yRule
{
    private const ENUM_MAP = [
        'aria-hidden' => ['true', 'false'],
        'aria-checked' => ['true', 'false', 'mixed'],
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

        if (!preg_match_all('/\b(aria-[a-z-]+)\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        foreach ($m as $pair) {
            $attr = strtolower($pair[1]);
            $value = $pair[2];
            // Ignore Twig dynamic expressions
            if (str_contains($value, '{{')) {
                continue;
            }

            if (str_contains($value, '{%')) {
                continue;
            }

            if (!isset(self::ENUM_MAP[$attr])) {
                continue;
            }

            if (!in_array(strtolower($value), self::ENUM_MAP[$attr], true)) {
                $token = $tokens->get(0);
                $emit(sprintf('Attribute %s has invalid value "%s".', $attr, $value), $token, 'AriaValidAttrValue.InvalidValue');
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
