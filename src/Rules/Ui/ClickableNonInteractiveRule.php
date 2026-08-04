<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Ui;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 4.1.2 / 2.1.1 — non-interactive elements (div, span, p, li, …) with an onclick
 * handler but without a tabindex attribute are not keyboard-reachable.
 * Use a <button> or add tabindex="0" and an appropriate role.
 */
final class ClickableNonInteractiveRule extends AbstractA11yRule
{
    /**
     * HTML elements that are natively interactive; onclick on these is acceptable.
     */
    private const array NATIVE_INTERACTIVE = [
        'a',
        'button',
        'input',
        'select',
        'textarea',
        'summary',
        'label',
        'option',
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), 'onclick')) {
            return;
        }

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

            if (!preg_match('/\bonclick\s*=/i', $attrs)) {
                continue;
            }

            // If there is already a tabindex, the developer has accounted for keyboard access.
            if (preg_match('/\btabindex\s*=/i', $attrs)) {
                continue;
            }

            ++$idx;
            $id = 'NonInteractiveOnclick';
            if ($idx > 1) {
                $id .= '#'.$idx;
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $match[0][0]);

            $emit(
                sprintf(
                    '<%s onclick="..."> is not keyboard-reachable. Use a <button>, or add tabindex="0" and a role (WCAG 4.1.2, 2.1.1).',
                    $tagName
                ),
                $fakeToken,
                $id
            );
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
