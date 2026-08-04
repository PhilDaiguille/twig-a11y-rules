<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Structure\TableLayoutRoleRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * RGAA 5.3 / WCAG 1.3.1 — a <table> used for layout (no <th> inside) must declare
 * role="presentation" or role="none" so assistive technologies ignore its structure.
 *
 * @see TableLayoutRoleRuleTest
 * @see TableLayoutRoleRuleTest
 * @see TableLayoutRoleRuleTest
 * @see TableLayoutRoleRuleTest
 * @see TableLayoutRoleRuleTest
 * @see TableLayoutRoleRuleTest
 */
final class TableLayoutRoleRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<table')) {
            return;
        }

        if (!preg_match_all('/<table\b([^>]*)>(.*?)<\/table>/is', $full, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($matches as $match) {
            $tableAttrs = $match[1][0];
            $tableBody = $match[2][0];
            $offset = $match[0][1];

            // If the table contains a <th>, it is a data table — not a layout table.
            if (preg_match('/<th\b/i', $tableBody)) {
                continue;
            }

            // If it already has role="presentation" or role="none", it is properly declared.
            if (preg_match('/\brole\s*=\s*(?:"|\')\s*(presentation|none)\s*(?:"|\')/i', $tableAttrs)) {
                continue;
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $match[0][0]);

            $emit(
                '<table> without <th> appears to be a layout table and should have role="presentation" or role="none" (RGAA 5.3, WCAG 1.3.1).',
                $fakeToken,
                'LayoutTableMissingRole'
            );
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
