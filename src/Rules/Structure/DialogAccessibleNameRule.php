<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class DialogAccessibleNameRule extends AbstractA11yRule
{
    #[\Override]
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<dialog') && !preg_match('/\brole\s*=\s*["\'](?:alertdialog|dialog)["\']/i', $full)) {
            return;
        }

        $matches = [];
        if (!preg_match_all(
            '/<(dialog)\b[^>]*>|<([a-z0-9:-]+)\b[^>]*\brole\s*=\s*["\'](alertdialog|dialog)["\'][^>]*>/i',
            $full,
            $matches,
            PREG_OFFSET_CAPTURE,
        )) {
            return;
        }

        foreach ($matches[0] as $match) {
            $tag = $match[0];
            $offset = $match[1];

            $ariaLabelMatch = [];
            if (preg_match('/\baria-label\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $tag, $ariaLabelMatch)) {
                $label = $ariaLabelMatch[1];
                if ('' !== trim($label)) {
                    continue;
                }
            }

            $ariaLabelledByMatch = [];
            if (preg_match('/\baria-labelledby\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $tag, $ariaLabelledByMatch)) {
                $labelledBy = $ariaLabelledByMatch[1];
                if ('' !== trim($labelledBy)) {
                    continue;
                }
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $tag);

            $emit(
                'Dialog element must have an accessible name via aria-label or aria-labelledby.',
                $fakeToken,
                'Missing',
            );

            return;
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
