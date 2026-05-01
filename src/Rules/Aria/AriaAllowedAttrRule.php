<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaAllowedAttrRule extends AbstractA11yRule
{
    /**
     * Allowed WAI-ARIA attributes per explicit role.
     *
     * IMPORTANT — coverage note: this list covers a practical subset of the
     * full WAI-ARIA 1.2 specification. Roles not listed here are silently
     * skipped (no false positive), but also not validated (potential false
     * negative). Expand this map as needed; the authoritative source is
     * https://www.w3.org/TR/wai-aria-1.2/#role_definitions
     *
     * All roles inherit the global ARIA attributes (aria-label,
     * aria-labelledby, aria-describedby, aria-hidden, aria-live, etc.), so
     * those are included for every role in the list.
     *
     * @var array<string, string[]>
     */
    private array $allowed = [
        // Global attrs shared by all roles — repeated per-role for explicitness.
        'button' => [
            'aria-pressed', 'aria-expanded', 'aria-haspopup', 'aria-disabled',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
            'aria-controls', 'aria-owns',
        ],
        'textbox' => [
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
            'aria-required', 'aria-invalid', 'aria-readonly', 'aria-disabled',
            'aria-multiline', 'aria-placeholder', 'aria-autocomplete',
        ],
        'img' => [
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
            'aria-roledescription',
        ],
        'checkbox' => [
            'aria-checked', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden', 'aria-required', 'aria-disabled', 'aria-readonly',
        ],
        'radio' => [
            'aria-checked', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden', 'aria-required', 'aria-disabled', 'aria-posinset',
            'aria-setsize',
        ],
        'combobox' => [
            'aria-expanded', 'aria-haspopup', 'aria-autocomplete', 'aria-required',
            'aria-invalid', 'aria-readonly', 'aria-disabled', 'aria-activedescendant',
            'aria-controls', 'aria-owns', 'aria-label', 'aria-labelledby',
            'aria-describedby', 'aria-hidden',
        ],
        'listbox' => [
            'aria-multiselectable', 'aria-required', 'aria-disabled', 'aria-readonly',
            'aria-activedescendant', 'aria-orientation', 'aria-label', 'aria-labelledby',
            'aria-describedby', 'aria-hidden', 'aria-owns',
        ],
        'option' => [
            'aria-selected', 'aria-disabled', 'aria-checked', 'aria-posinset',
            'aria-setsize', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden',
        ],
        'tab' => [
            'aria-selected', 'aria-disabled', 'aria-expanded', 'aria-controls',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
            'aria-posinset', 'aria-setsize',
        ],
        'tabpanel' => [
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'dialog' => [
            'aria-modal', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden',
        ],
        'alertdialog' => [
            'aria-modal', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden',
        ],
        'grid' => [
            'aria-multiselectable', 'aria-readonly', 'aria-disabled', 'aria-rowcount',
            'aria-colcount', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden', 'aria-activedescendant',
        ],
        'gridcell' => [
            'aria-selected', 'aria-readonly', 'aria-required', 'aria-disabled',
            'aria-expanded', 'aria-colspan', 'aria-rowspan', 'aria-colindex',
            'aria-rowindex', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden',
        ],
        'row' => [
            'aria-selected', 'aria-expanded', 'aria-level', 'aria-rowindex',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'rowgroup' => [
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'columnheader' => [
            'aria-sort', 'aria-readonly', 'aria-required', 'aria-selected',
            'aria-colspan', 'aria-rowspan', 'aria-colindex', 'aria-rowindex',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'rowheader' => [
            'aria-sort', 'aria-readonly', 'aria-required', 'aria-selected',
            'aria-colspan', 'aria-rowspan', 'aria-colindex', 'aria-rowindex',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'slider' => [
            'aria-valuenow', 'aria-valuemin', 'aria-valuemax', 'aria-valuetext',
            'aria-orientation', 'aria-disabled', 'aria-readonly', 'aria-label',
            'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'spinbutton' => [
            'aria-valuenow', 'aria-valuemin', 'aria-valuemax', 'aria-valuetext',
            'aria-required', 'aria-invalid', 'aria-readonly', 'aria-disabled',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'progressbar' => [
            'aria-valuenow', 'aria-valuemin', 'aria-valuemax', 'aria-valuetext',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'scrollbar' => [
            'aria-valuenow', 'aria-valuemin', 'aria-valuemax', 'aria-orientation',
            'aria-controls', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden', 'aria-disabled',
        ],
        'separator' => [
            'aria-valuenow', 'aria-valuemin', 'aria-valuemax', 'aria-orientation',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
            'aria-disabled',
        ],
        'menuitem' => [
            'aria-disabled', 'aria-expanded', 'aria-haspopup', 'aria-posinset',
            'aria-setsize', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden',
        ],
        'menuitemcheckbox' => [
            'aria-checked', 'aria-disabled', 'aria-expanded', 'aria-haspopup',
            'aria-posinset', 'aria-setsize', 'aria-label', 'aria-labelledby',
            'aria-describedby', 'aria-hidden',
        ],
        'menuitemradio' => [
            'aria-checked', 'aria-disabled', 'aria-posinset', 'aria-setsize',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'treeitem' => [
            'aria-expanded', 'aria-selected', 'aria-checked', 'aria-level',
            'aria-posinset', 'aria-setsize', 'aria-disabled', 'aria-label',
            'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'tree' => [
            'aria-multiselectable', 'aria-required', 'aria-activedescendant',
            'aria-orientation', 'aria-label', 'aria-labelledby', 'aria-describedby',
            'aria-hidden',
        ],
        'link' => [
            'aria-disabled', 'aria-expanded', 'aria-haspopup', 'aria-label',
            'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
        'switch' => [
            'aria-checked', 'aria-readonly', 'aria-required', 'aria-disabled',
            'aria-label', 'aria-labelledby', 'aria-describedby', 'aria-hidden',
        ],
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

        $idx = 0;

        if (preg_match_all('/<([a-z0-9]+)([^>]*)\srole\s*=\s*(?:"|\')([^"\']+)(?:"|\')([^>]*)>/i', $full, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $role = strtolower($m[3]);
                $attrs = $m[2].' '.$m[4];
                if (!isset($this->allowed[$role])) {
                    continue;
                }

                // naive check: find any aria- attribute not in allowed list
                if (preg_match_all('/\baria-[a-z0-9-]+\s*=\s*(?:"|\')[^"\']*(?:"|\')/i', $attrs, $am)) {
                    foreach ($am[0] as $ariaRaw) {
                        if (preg_match('/\baria-([a-z0-9-]+)/i', $ariaRaw, $an)) {
                            $name = strtolower($an[1]);
                            if (!in_array('aria-'.$name, $this->allowed[$role], true)) {
                                ++$idx;
                                $fakeToken = $tokens->get(0);
                                $id = 1 === $idx ? 'AriaAllowed.Invalid' : sprintf('AriaAllowed.Invalid#%d', $idx);
                                $emit(sprintf('Attribute aria-%s is not allowed on role %s.', $name, $role), $fakeToken, $id);
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
