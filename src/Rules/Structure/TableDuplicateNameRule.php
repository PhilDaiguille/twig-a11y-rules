<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class TableDuplicateNameRule extends AbstractA11yRule
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

        if (!preg_match_all('/<table\b([^>]*)>(.*?)<\/table>/is', $full, $tables, PREG_SET_ORDER)) {
            return;
        }

        foreach ($tables as $tbl) {
            // preg_match_all with PREG_SET_ORDER guarantees capture groups
            // when a match is present.
            $attrs = $tbl[1];
            $content = $tbl[2];

            $summary = '';
            if (preg_match('/\bsummary\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $m)) {
                $summary = $this->firstMatch($m, 1, 2);
            }

            $caption = '';
            if (preg_match('/<caption[^>]*>(.*?)<\/caption>/is', $content, $c)) {
                $caption = trim(strip_tags($c[1]));
            }

            if ('' !== $summary && '' !== $caption) {
                $n1 = strtolower(trim($summary));
                $n2 = strtolower(trim($caption));

                if ($n1 === $n2) {
                    $token = $tokens->get(0);
                    $emit('Table summary duplicates caption content; provide distinct descriptions.', $token, 'TableDuplicate.Duplicate');

                    return;
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
