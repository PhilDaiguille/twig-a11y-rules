<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

/**
 * Detects <p> elements that use inline bold/font-size styling to visually
 * mimic a heading, which is not machine-readable and breaks accessibility.
 *
 * Checks for:
 *  - <p style="font-weight: bold|700|800|900">
 *  - <p style="font-size: Xem|px"> where value is large enough to look like a heading
 *    (>= 1.5em or >= 20px as a simple heuristic)
 *
 * WCAG 1.3.1 — Info and Relationships, Level A.
 */
final class PAsHeadingRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), '<p')) {
            return;
        }

        // Find <p ...> opening tags that have a style attribute.
        if (!preg_match_all('/<p\b([^>]*style\s*=\s*(?:"[^"]*"|\'[^\']*\')[^>]*)>/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        $idx = 0;
        foreach ($m as $set) {
            $attrs = $set[1];

            // Extract the style attribute value.
            if (!preg_match('/\bstyle\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $styleMatch)) {
                continue;
            }

            $style = strtolower('' !== $styleMatch[1] ? $styleMatch[1] : ($styleMatch[2] ?? ''));

            $isFakeHeading = false;

            // Bold weight
            if (preg_match('/\bfont-weight\s*:\s*(bold|[789]\d\d)\b/', $style)) {
                $isFakeHeading = true;
            }

            // Large font size (>= 1.5em or >= 20px)
            if (!$isFakeHeading && preg_match('/\bfont-size\s*:\s*(\d*\.?\d+)(em|px)\b/', $style, $sizeMatch)) {
                $value = (float) $sizeMatch[1];
                $unit = $sizeMatch[2];
                if ('em' === $unit && $value >= 1.5) {
                    $isFakeHeading = true;
                } elseif ('px' === $unit && $value >= 20) {
                    $isFakeHeading = true;
                }
            }

            if ($isFakeHeading) {
                ++$idx;
                $id = 'PAsHeading.FakeHeading';
                if ($idx > 1) {
                    $id .= '#'.$idx;
                }

                $emit(
                    'Avoid using a <p> with bold/large-font styling as a heading; use a semantic heading element (<h1>–<h6>) instead.',
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
