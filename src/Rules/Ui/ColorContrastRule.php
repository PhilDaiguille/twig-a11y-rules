<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Ui;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class ColorContrastRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 !== $tokenIndex) {
            return;
        }

        $content = $this->getFullContent($tokens);

        if (!preg_match_all('/style\s*=\s*["\']([^"\']+)["\']/i', $content, $matches)) {
            return;
        }

        $firstToken = $tokens->get(0);
        foreach ($matches[1] as $style) {
            $bg = $this->extractColor($style, 'background-color');
            $fg = $this->extractColor($style, 'color');
            if (null === $fg) {
                continue;
            }

            if (null === $bg) {
                continue;
            }

            $ratio = $this->contrastRatio($fg, $bg);
            if ($ratio < 4.5) {
                $emit('Insufficient color contrast', $firstToken, 'ColorContrast.Insufficient');

                return;
            }
        }
    }

    /**
     * @return null|array{int, int, int}
     */
    private function extractColor(string $style, string $prop): ?array
    {
        // Fix: pass '/' as the delimiter argument to preg_quote
        if (!preg_match('/'.preg_quote($prop, '/').'\s*:\s*([^;]+)(?:;|$)/i', $style, $m)) {
            return null;
        }

        $c = trim($m[1]);

        if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $c)) {
            return $this->hexToRgb($c);
        }

        if (preg_match('/rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/i', $c, $mm)) {
            return [(int) $mm[1], (int) $mm[2], (int) $mm[3]];
        }

        return null;
    }

    /**
     * @return array{int, int, int}
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (3 === strlen($hex)) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * @param array{int, int, int} $rgb
     */
    private function lum(array $rgb): float
    {
        // Fix: explicitly cast to float so PHPStan knows the array values are numeric
        [$r, $g, $b] = array_map(fn (int $v): float => (float) $v / 255.0, $rgb);

        // Fix: narrow the return type to float only — the ternary always returns float
        $f = fn (float $c): float => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;

        return 0.2126 * $f($r) + 0.7152 * $f($g) + 0.0722 * $f($b);
    }

    /**
     * @param array{int, int, int} $fg
     * @param array{int, int, int} $bg
     */
    private function contrastRatio(array $fg, array $bg): float
    {
        $l1 = $this->lum($fg);
        $l2 = $this->lum($bg);
        $light = max($l1, $l2);
        $dark = min($l1, $l2);

        return ($light + 0.05) / ($dark + 0.05);
    }
}
