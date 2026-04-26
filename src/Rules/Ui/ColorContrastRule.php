<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Ui;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ColorContrastRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Perform a single document-level scan to find inline style attributes
        // and evaluate contrast where both color and background-color are
        // specified.
        if (0 !== $tokenIndex) {
            return;
        }

        $content = '';
        foreach ($tokens->toArray() as $t) {
            $content .= (string) $t->getValue();
        }

        if (!preg_match_all('/style\s*=\s*["\']([^"\']+)["\']/i', $content, $matches)) {
            return;
        }

        $firstToken = $tokens->get(0);
        foreach ($matches[1] as $style) {
            $bg = $this->extractColor($style, 'background-color');
            $fg = $this->extractColor($style, 'color');

            if (null === $fg || null === $bg) {
                continue;
            }

            $ratio = $this->contrastRatio($fg, $bg);
            if ($ratio < 4.5) {
                $emit('Insufficient color contrast', $firstToken, 'ColorContrast.Insufficient');

                // emit only once per document
                return;
            }
        }
    }

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $emit = function (string $message, Token $token, ?string $id = null): void {
            $this->addError($message, $token, $id);
        };

        $this->evaluate($tokens, $tokenIndex, $emit);
    }

    private function extractColor(string $style, string $prop): ?array
    {
        if (!preg_match('/'.preg_quote($prop).'\s*:\s*([^;]+)(?:;|$)/i', $style, $m)) {
            return null;
        }

        $c = trim($m[1]);
        // hex
        if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $c, $mm)) {
            return $this->hexToRgb($c);
        }

        // rgb(a)
        if (preg_match('/rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/i', $c, $mm)) {
            return [(int) $mm[1], (int) $mm[2], (int) $mm[3]];
        }

        return null;
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (3 === strlen($hex)) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    private function lum(array $rgb): float
    {
        [$r, $g, $b] = array_map(fn ($v): float => $v / 255.0, $rgb);
        $f = fn ($c): float|int|object => $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);

        return 0.2126 * $f($r) + 0.7152 * $f($g) + 0.0722 * $f($b);
    }

    private function contrastRatio(array $fg, array $bg): float
    {
        $l1 = $this->lum($fg);
        $l2 = $this->lum($bg);
        $light = max($l1, $l2);
        $dark = min($l1, $l2);

        return ($light + 0.05) / ($dark + 0.05);
    }
}
