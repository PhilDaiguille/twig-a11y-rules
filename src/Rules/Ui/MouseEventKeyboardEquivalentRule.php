<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Ui;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Ui\MouseEventKeyboardEquivalentRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 2.1.1 — mouse-only event handlers (onmousedown, onmouseover, onmouseout)
 * must have keyboard equivalents (onkeydown/onkeyup/onkeypress, onfocus, onblur)
 * on the same element so that keyboard-only users can trigger the same behaviour.
 *
 * @see MouseEventKeyboardEquivalentRuleTest
 * @see MouseEventKeyboardEquivalentRuleTest
 * @see MouseEventKeyboardEquivalentRuleTest
 * @see MouseEventKeyboardEquivalentRuleTest
 * @see MouseEventKeyboardEquivalentRuleTest
 * @see MouseEventKeyboardEquivalentRuleTest
 */
final class MouseEventKeyboardEquivalentRule extends AbstractA11yRule
{
    /**
     * Mouse handlers that must have a keyboard counterpart on the same element.
     * Key = mouse event, value = list of acceptable keyboard counterparts.
     *
     * @var array<string, list<string>>
     */
    private const PAIRS = [
        'onmousedown' => ['onkeydown', 'onkeyup', 'onkeypress'],
        'onmouseover' => ['onfocus'],
        'onmouseout' => ['onblur'],
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);
        $fullLower = strtolower($full);

        // Quick pre-check: at least one mouse event must be present.
        $hasMouseEvent = false;
        foreach (array_keys(self::PAIRS) as $mouseEvent) {
            if (str_contains($fullLower, $mouseEvent)) {
                $hasMouseEvent = true;

                break;
            }
        }

        if (!$hasMouseEvent) {
            return;
        }

        if (!preg_match_all('/<[a-z][a-z0-9]*\b([^>]*)\s*>/is', $full, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        $idx = 0;
        foreach ($matches as $match) {
            $attrs = $match[1][0];
            $attrsLower = strtolower($attrs);
            $offset = $match[0][1];

            foreach (self::PAIRS as $mouseEvent => $keyboardEquivalents) {
                if (!str_contains($attrsLower, $mouseEvent)) {
                    continue;
                }

                $hasKeyboard = false;
                foreach ($keyboardEquivalents as $kbEvent) {
                    if (str_contains($attrsLower, $kbEvent)) {
                        $hasKeyboard = true;

                        break;
                    }
                }

                if ($hasKeyboard) {
                    continue;
                }

                ++$idx;
                $id = 'MouseOnlyEvent';
                if ($idx > 1) {
                    $id .= '#'.$idx;
                }

                $line = 1 + substr_count(substr($full, 0, $offset), "\n");
                $fakeToken = $this->fakeTokenForLine($tokens, $line, $match[0][0]);

                $emit(
                    sprintf(
                        'Mouse event handler "%s" has no keyboard equivalent (%s) (WCAG 2.1.1).',
                        $mouseEvent,
                        implode(' or ', $keyboardEquivalents)
                    ),
                    $fakeToken,
                    $id
                );
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
