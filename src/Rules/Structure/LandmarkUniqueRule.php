<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class LandmarkUniqueRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Page-level rule: only evaluate once per file. Use the helper for
        // backwards-compatibility with older rules.
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        // Landmarks to consider by tag name. We also map to their ARIA role
        // equivalents to detect landmarks implemented via role="...".
        $landmarks = [
            'main' => 'main',
            'nav' => 'navigation',
            'aside' => 'complementary',
            'header' => 'banner',
            'footer' => 'contentinfo',
        ];

        foreach ($landmarks as $tag => $role) {
            $occurrences = [];

            // Small helper to extract an attribute value from an opening tag
            $extractAttr = function (string $opening, string $attr): string {
                $pos = stripos($opening, $attr);
                if (false === $pos) {
                    return '';
                }

                $eq = strpos($opening, '=', $pos);
                if (false === $eq) {
                    return '';
                }

                $i = $eq + 1;
                $len = strlen($opening);
                while ($i < $len && ctype_space($opening[$i])) {
                    ++$i;
                }

                if ($i >= $len) {
                    return '';
                }

                $quote = $opening[$i];
                if ('"' === $quote || "'" === $quote) {
                    $j = $i + 1;
                    $val = '';
                    while ($j < $len && $opening[$j] !== $quote) {
                        $val .= $opening[$j];
                        ++$j;
                    }

                    return $val;
                }

                // Unquoted attr value
                $j = $i;
                $val = '';
                while ($j < $len && !ctype_space($opening[$j]) && '>' !== $opening[$j]) {
                    $val .= $opening[$j];
                    ++$j;
                }

                return $val;
            };

            // Find explicit tag occurrences: <nav ...>, <aside ...> etc.
            $patternTag = sprintf('/<\s*%s\b[^>]*>/i', preg_quote($tag, '/'));
            if (preg_match_all($patternTag, $full, $m, PREG_OFFSET_CAPTURE)) {
                foreach ($m[0] as $match) {
                    $opening = $match[0];
                    // Extract aria-label / aria-labelledby (use simple parser
                    // to avoid complex regex quoting in patterns).
                    $label = $extractAttr($opening, 'aria-label');
                    if ('' === $label) {
                        $label = $extractAttr($opening, 'aria-labelledby');
                    }

                    $occurrences[] = ['opening' => $opening, 'label' => trim($label)];
                }
            }

            // Find elements using role="..." for the equivalent landmark role.
            $roleEsc = preg_quote($role, '/');
            $patternRole = sprintf('/<\s*([a-zA-Z0-9:_-]+)\b[^>]*role\s*=\s*(?:"%s"|\'%s\')[^>]*>/i', $roleEsc, $roleEsc);
            if (preg_match_all($patternRole, $full, $mr, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
                foreach ($mr as $match) {
                    // $match[0] is the full tag, $match[1] is the tag name
                    $tagName = strtolower($match[1][0]);
                    // If this is the same tag we've already counted via tag match,
                    // skip to avoid double-counting (eg. <main role="main">).
                    if ($tagName === $tag) {
                        continue;
                    }

                    $opening = $match[0][0];
                    $label = $extractAttr($opening, 'aria-label');
                    if ('' === $label) {
                        $label = $extractAttr($opening, 'aria-labelledby');
                    }

                    $occurrences[] = ['opening' => $opening, 'label' => trim($label)];
                }
            }

            $count = count($occurrences);
            if ($count <= 1) {
                continue;
            }

            // For the main landmark, keep the old strict behaviour: only one
            // main landmark is allowed on a page regardless of labels.
            if ('main' === $tag) {
                $fake = $tokens->get(0);
                $emit(sprintf('Landmark role="%s" appears %d times; landmarks should be unique.', $role, $count), $fake, 'Landmark.Unique');

                return;
            }

            // For other landmark types, warn when multiple occurrences lack
            // distinct labels (aria-label / aria-labelledby).
            // Each $occurrence has a 'label' key (possibly empty string).
            $labels = array_map(fn (array $o): string => $o['label'], $occurrences);
            $hasEmpty = in_array('', $labels, true);
            $unique = count(array_unique($labels));

            if ($hasEmpty || $unique < $count) {
                $fake = $tokens->get(0);
                $emit(sprintf('Multiple %s landmarks found; ensure each has a distinct aria-label or aria-labelledby.', $tag), $fake, 'Landmark.Unique');

                return;
            }
        }
    }
}
