<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AriaRoleRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Run once per file to avoid duplicate reports and to be robust against tokenization
        if (0 !== $tokenIndex) {
            return;
        }

        // Scan entire token stream to be robust against tokenization
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

            // report only once per file/token stream
            foreach ($roles as $role) {
                if (!in_array($role, $allowed, true)) {
                    $tokenRef = $tokens->get(0);
                    $emit(sprintf('Invalid ARIA role "%s".', $role), $tokenRef, 'AriaRole.InvalidRole');

                    // stop after first invalid role for determinism in tests
                    return;
                }
            }
        }
    }
}
