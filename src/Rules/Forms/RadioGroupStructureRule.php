<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class RadioGroupStructureRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, 'type="radio"') && !str_contains($full, "type='radio'")) {
            return;
        }

        if (!preg_match_all('/<input\b[^>]*\btype\s*=\s*(?:"radio"|\'radio\')[^>]*\bname\s*=\s*(?:"([^"]+)"|\'([^\']+)\')[^>]*>/i', $full, $radios, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        $groups = [];
        foreach ($radios as $radio) {
            $name = $this->firstMatch([
                1 => $radio[1][0] ?? null,
                2 => $radio[2][0] ?? null,
            ], 1, 2);
            $groups[$name][] = [
                'tag' => $radio[0][0],
                'offset' => $radio[0][1],
            ];
        }

        foreach ($groups as $name => $items) {
            if (count($items) < 2) {
                continue;
            }

            if ($this->isInsideFieldsetGroup($full, $name)) {
                continue;
            }

            if ($this->isInsideAriaRadioGroup($full, $name)) {
                continue;
            }

            $line = 1 + substr_count(substr($full, 0, $items[0]['offset']), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $items[0]['tag']);

            $emit(sprintf('Radio inputs sharing name "%s" should be grouped inside a <fieldset> or a container with role="radiogroup".', $name), $fakeToken, 'RadioGroupStructure.MissingGroup');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    private function isInsideFieldsetGroup(string $full, string $name): bool
    {
        if (!preg_match_all('/<fieldset\b[^>]*>(.*?)<\/fieldset>/is', $full, $fieldsets, PREG_SET_ORDER)) {
            return false;
        }

        foreach ($fieldsets as $fieldset) {
            $content = $fieldset[1];
            $count = preg_match_all('/<input\b[^>]*\btype\s*=\s*(?:"radio"|\'radio\')[^>]*\bname\s*=\s*(?:"'.preg_quote($name, '/').'"|\''.preg_quote($name, '/')."')[^>]*>/i", $content);
            if ($count >= 2) {
                return true;
            }
        }

        return false;
    }

    private function isInsideAriaRadioGroup(string $full, string $name): bool
    {
        if (!preg_match_all('/<(div|section|fieldset)\b[^>]*\brole\s*=\s*(?:"radiogroup"|\'radiogroup\')[^>]*>(.*?)<\/\1>/is', $full, $groups, PREG_SET_ORDER)) {
            return false;
        }

        foreach ($groups as $group) {
            $content = $group[2];
            $count = preg_match_all('/<input\b[^>]*\btype\s*=\s*(?:"radio"|\'radio\')[^>]*\bname\s*=\s*(?:"'.preg_quote($name, '/').'"|\''.preg_quote($name, '/')."')[^>]*>/i", $content);
            if ($count >= 2) {
                return true;
            }
        }

        return false;
    }
}
