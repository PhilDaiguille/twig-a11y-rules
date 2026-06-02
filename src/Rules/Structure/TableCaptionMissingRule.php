<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class TableCaptionMissingRule extends AbstractA11yRule
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

        if (!preg_match_all('/<table\b[^>]*>(.*?)<\/table>/is', $full, $tables, PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($tables[0] as $index => $match) {
            $tableBlock = $match[0];
            $offset = $match[1];
            $inner = $tables[1][$index][0];

            if (!$this->looksLikeDataTable($tableBlock, $inner)) {
                continue;
            }

            if (preg_match('/<caption\b[^>]*>(.*?)<\/caption>/is', $inner, $captionMatch) && '' !== trim(strip_tags($captionMatch[1]))) {
                continue;
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $tableBlock);

            $emit('Data tables should include a non-empty <caption> element.', $fakeToken, 'TableCaption.MissingCaption');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    private function looksLikeDataTable(string $tableBlock, string $inner): bool
    {
        return (bool) preg_match('/<th\b|\bscope\s*=|\bheaders\s*=|<thead\b|<tbody\b|<tfoot\b|\bsummary\s*=/i', $tableBlock.$inner);
    }
}
