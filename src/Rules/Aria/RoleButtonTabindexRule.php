<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Aria\RoleButtonTabindexRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 4.1.2 / 2.1.1 — non-native interactive elements using an interactive ARIA role
 * (button, link, checkbox, radio, tab, menuitem, …) must have tabindex="0" so that
 * keyboard users can reach them via Tab navigation.
 *
 * @see RoleButtonTabindexRuleTest
 * @see RoleButtonTabindexRuleTest
 * @see RoleButtonTabindexRuleTest
 * @see RoleButtonTabindexRuleTest
 * @see RoleButtonTabindexRuleTest
 * @see RoleButtonTabindexRuleTest
 */
final class RoleButtonTabindexRule extends AbstractA11yRule
{
    /**
     * Interactive ARIA roles that require keyboard reachability when placed on
     * elements that are not natively focusable.
     */
    private const INTERACTIVE_ROLES = [
        'button',
        'link',
        'checkbox',
        'radio',
        'tab',
        'menuitem',
        'menuitemcheckbox',
        'menuitemradio',
        'option',
        'switch',
        'treeitem',
        'gridcell',
    ];

    /**
     * HTML elements that are natively focusable and do not need an explicit tabindex.
     */
    private const NATIVE_INTERACTIVE = [
        'a',
        'button',
        'input',
        'select',
        'textarea',
        'summary',
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, 'role=')) {
            return;
        }

        $rolePattern = implode('|', array_map(preg_quote(...), self::INTERACTIVE_ROLES));

        if (!preg_match_all('/<([a-z][a-z0-9]*)\b([^>]*)\s*>/is', $full, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        $idx = 0;
        foreach ($matches as $match) {
            $tagName = strtolower($match[1][0]);
            $attrs = $match[2][0];
            $offset = $match[0][1];

            if (in_array($tagName, self::NATIVE_INTERACTIVE, true)) {
                continue;
            }

            if (!preg_match('/\brole\s*=\s*["\'\s]*('.$rolePattern.')\s*["\']/i', $attrs, $roleMatch)) {
                continue;
            }

            if (preg_match('/\btabindex\s*=/i', $attrs)) {
                continue;
            }

            ++$idx;
            $id = 'MissingTabindex';
            if ($idx > 1) {
                $id .= '#'.$idx;
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $match[0][0]);

            $emit(
                sprintf(
                    '<%s role="%s"> is not natively focusable and must have tabindex="0" to be keyboard-reachable (WCAG 4.1.2, 2.1.1).',
                    $tagName,
                    $roleMatch[1]
                ),
                $fakeToken,
                $id
            );
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
