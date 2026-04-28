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
        'aria-expanded' => ['true', 'false'],
        'aria-selected' => ['true', 'false'],
        'aria-pressed' => ['true', 'false', 'mixed'],
        'aria-disabled' => ['true', 'false'],
        'aria-required' => ['true', 'false'],
        'aria-readonly' => ['true', 'false'],
        'aria-multiline' => ['true', 'false'],
        'aria-multiselectable' => ['true', 'false'],
        'aria-modal' => ['true', 'false'],
        'aria-busy' => ['true', 'false'],
        'aria-atomic' => ['true', 'false'],
        'aria-grabbed' => ['true', 'false'],
        'aria-sort' => ['none', 'ascending', 'descending', 'other'],
        'aria-live' => ['off', 'assertive', 'polite'],
        'aria-relevant' => ['additions', 'additions text', 'all', 'removals', 'text'],
        'aria-orientation' => ['horizontal', 'vertical', 'undefined'],
        'aria-haspopup' => ['false', 'true', 'menu', 'listbox', 'tree', 'grid', 'dialog'],
        'aria-autocomplete' => ['none', 'inline', 'list', 'both'],
        'aria-current' => ['false', 'true', 'page', 'step', 'location', 'date', 'time'],
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
            // Ignore Twig dynamic expressions using the trait helper which
            // checks for {{ and {% via str_contains (no regex overhead).
            if ($this->containsTwigExpressions($value)) {
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
