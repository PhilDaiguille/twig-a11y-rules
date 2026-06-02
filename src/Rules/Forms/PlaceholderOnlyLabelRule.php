<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class PlaceholderOnlyLabelRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, 'placeholder=')) {
            return;
        }

        if (!preg_match_all('/<(input|textarea)\b[^>]*\bplaceholder\s*=\s*(?:"[^"]+"|\'[^\']+\')[^>]*>/i', $full, $matches, PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($matches[0] as $match) {
            $tag = $match[0];
            $offset = $match[1];

            if (preg_match('/<input\b[^>]*\btype\s*=\s*["\'](?:hidden|submit|button|reset|image|checkbox|radio|file)["\']/i', $tag)) {
                continue;
            }

            if ($this->openingProvidesLabel($tag)) {
                continue;
            }

            $id = $this->extractFirstId($tag);
            if ('' !== $id && $this->hasLabelFor($full, $id)) {
                continue;
            }

            $tagName = str_starts_with(strtolower($tag), '<textarea') ? 'textarea' : 'input';
            if (preg_match('/<label[^>]*>\s*<'.preg_quote($tagName, '/').'\b[^>]*>/i', $full)) {
                continue;
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $tag);

            $emit('Form field appears to rely on placeholder text instead of a proper label.', $fakeToken, 'PlaceholderOnlyLabel.MissingLabel');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
