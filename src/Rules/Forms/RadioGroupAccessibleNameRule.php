<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class RadioGroupAccessibleNameRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, 'type="radio"') && !str_contains($full, "type='radio'")) {
            return;
        }

        if (preg_match_all('/<fieldset\b[^>]*>(.*?)<\/fieldset>/is', $full, $fieldsets, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            foreach ($fieldsets as $fieldset) {
                $fieldsetBlock = $fieldset[0][0];
                $offset = $fieldset[0][1];
                $content = $fieldset[1][0];

                $radioCount = preg_match_all('/<input\b[^>]*\btype\s*=\s*(?:"radio"|\'radio\')[^>]*>/i', $content);
                if ($radioCount < 2) {
                    continue;
                }

                if (preg_match('/<legend\b[^>]*>\s*([^<]+?)\s*<\/legend>/i', $fieldsetBlock, $legendMatch) && '' !== trim($legendMatch[1])) {
                    continue;
                }

                $line = 1 + substr_count(substr($full, 0, $offset), "\n");
                $fakeToken = $this->fakeTokenForLine($tokens, $line, $fieldsetBlock);
                $emit('Fieldsets containing radio groups should provide a non-empty <legend>.', $fakeToken, 'RadioGroupAccessibleName.MissingLegend');

                return;
            }
        }

        if (!preg_match_all('/<(div|section|fieldset)\b[^>]*\brole\s*=\s*(?:"radiogroup"|\'radiogroup\')[^>]*>(.*?)<\/\1>/is', $full, $groups, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($groups as $group) {
            $groupBlock = $group[0][0];
            $offset = $group[0][1];
            $content = $group[2][0];

            $radioCount = preg_match_all('/<input\b[^>]*\btype\s*=\s*(?:"radio"|\'radio\')[^>]*>/i', $content);
            if ($radioCount < 2) {
                continue;
            }

            if ($this->hasNonEmptyReference($groupBlock, 'aria-labelledby')) {
                continue;
            }

            if ($this->hasNonEmptyReference($groupBlock, 'aria-label')) {
                continue;
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $groupBlock);
            $emit('Containers with role="radiogroup" should have an accessible name via aria-label or aria-labelledby.', $fakeToken, 'RadioGroupAccessibleName.MissingName');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    private function hasNonEmptyReference(string $tag, string $attribute): bool
    {
        if (!preg_match('/\b'.preg_quote($attribute, '/').'\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $tag, $match)) {
            return false;
        }

        return '' !== trim($this->firstMatch($match, 1, 2));
    }
}
