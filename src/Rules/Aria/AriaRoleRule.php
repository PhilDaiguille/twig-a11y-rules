<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class AriaRoleRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        // Page-level scan across the whole template
        $tag = $this->getFullContent($tokens);

        if (preg_match_all('/role\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $tag, $m)) {
            $roles = array_map(strtolower(...), $m[1]);

            // expanded list of common ARIA roles
            $allowed = [
                'alert', 'alertdialog', 'application', 'article', 'banner', 'button', 'checkbox', 'columnheader',
                'combobox', 'complementary', 'contentinfo', 'dialog', 'directory', 'document', 'feed', 'figure',
                'form', 'grid', 'gridcell', 'group', 'heading', 'img', 'link', 'list', 'listbox', 'listitem', 'log',
                'main', 'math', 'menu', 'menubar', 'menuitem', 'menuitemcheckbox', 'menuitemradio',
                'navigation', 'none', 'note', 'option', 'presentation', 'progressbar', 'radio', 'radiogroup', 'region',
                'row', 'rowgroup', 'rowheader', 'search', 'separator', 'slider', 'spinbutton', 'status', 'switch', 'tab',
                'table', 'tablist', 'tabpanel', 'textbox', 'timer', 'toolbar', 'tooltip', 'tree', 'treegrid', 'treeitem',
            ];

            // Collect and report all invalid roles found in the document.
            $invalid = [];
            foreach ($roles as $role) {
                if (!in_array($role, $allowed, true)) {
                    $invalid[] = $role;
                }
            }

            if ([] !== $invalid) {
                $tokenRef = $tokens->get(0);
                $idx = 0;
                foreach ($invalid as $role) {
                    ++$idx;
                    $id = 'AriaRole.InvalidRole';
                    if ($idx > 1) {
                        $id .= '#'.$idx;
                    }

                    $emit(sprintf('Invalid ARIA role "%s".', $role), $tokenRef, $id);
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
