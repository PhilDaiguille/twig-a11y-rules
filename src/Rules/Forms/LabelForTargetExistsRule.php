<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class LabelForTargetExistsRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<label')) {
            return;
        }

        $idCount = preg_match_all('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $full, $idMatches);
        $ids = [];
        if ($idCount > 0) {
            $ids = array_flip($idMatches[1]);
        }

        if (!preg_match_all('/<label\b[^>]*\bfor\s*=\s*(?:"([^"]+)"|\'([^\']+)\')[^>]*>/i', $full, $labels, PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($labels[0] as $index => $match) {
            $tag = $match[0];
            $offset = $match[1];
            $forId = $labels[1][$index][0] ?: $labels[2][$index][0];

            if (isset($ids[$forId])) {
                continue;
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $tag);

            $emit(sprintf('Label for="%s" does not reference any existing id in template.', $forId), $fakeToken, 'LabelFor.MissingTarget');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
