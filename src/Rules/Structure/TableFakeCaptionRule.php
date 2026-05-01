<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

/**
 * Detects the anti-pattern of using the first <td> in a table as a visual
 * caption instead of using a proper <caption> element.
 *
 * The heuristic: a table that has no <caption> element but whose first
 * data cell (the first <td> in the first <tr>) spans all columns via
 * colspan equal to the table's column count, or is the sole cell in the
 * first row, is flagged as likely using a fake caption.
 *
 * WCAG 1.3.1 — Info and Relationships, Level A.
 */
final class TableFakeCaptionRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<table')) {
            return;
        }

        // Extract each <table>...</table> block.
        if (!preg_match_all('/<table\b[^>]*>(.*?)<\/table>/is', $full, $tables, PREG_SET_ORDER)) {
            return;
        }

        $idx = 0;
        foreach ($tables as $tableSet) {
            $tableContent = $tableSet[1];

            // If a proper <caption> element is present, the table is fine.
            if (preg_match('/<caption\b/i', $tableContent)) {
                continue;
            }

            // Find the first <tr> in the table (may be inside <thead> or <tbody>).
            if (!preg_match('/<tr\b[^>]*>(.*?)<\/tr>/is', $tableContent, $firstRow)) {
                continue;
            }

            $rowContent = $firstRow[1];

            // Count how many <td> or <th> cells are in the first row.
            $cellCount = preg_match_all('/<t[dh]\b/i', $rowContent);

            // Flag when the first row contains exactly one <td> (sole cell),
            // which is the classic fake-caption pattern.
            if (1 === $cellCount && preg_match('/<td\b/i', $rowContent)) {
                ++$idx;
                $id = 'TableFakeCaption.FakeCaption';
                if ($idx > 1) {
                    $id .= '#'.$idx;
                }

                $emit(
                    'Avoid using a <td> as a table caption; use the <caption> element instead.',
                    $tokens->get(0),
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
