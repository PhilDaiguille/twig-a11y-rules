<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class DetailsSummaryRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<details')) {
            return;
        }

        if (!preg_match_all('/<details\b[^>]*>(.*?)<\/details>/is', $full, $matches, PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($matches[0] as $match) {
            $detailsBlock = $match[0];
            $offset = $match[1];
            $line = 1 + substr_count(substr($full, 0, $offset), "\n");

            $fakeToken = $this->fakeTokenForLine($tokens, $line, $detailsBlock);

            if (!preg_match('/<summary\b[^>]*>(.*?)<\/summary>/is', $detailsBlock, $summaryMatch)) {
                $emit('Details element must contain a non-empty <summary>.', $fakeToken, 'DetailsSummary.MissingSummary');

                return;
            }

            $summaryText = trim(strip_tags($summaryMatch[1]));

            if ('' === $summaryText) {
                $emit('Summary element inside <details> must have non-empty content.', $fakeToken, 'DetailsSummary.EmptySummary');

                return;
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
