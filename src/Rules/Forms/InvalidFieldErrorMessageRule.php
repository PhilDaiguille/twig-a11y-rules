<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class InvalidFieldErrorMessageRule extends AbstractA11yRule
{
    private const CUSTOM_FIELD_ROLES = [
        'textbox',
        'combobox',
        'searchbox',
        'spinbutton',
        'listbox',
        'radiogroup',
    ];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, 'aria-invalid')) {
            return;
        }

        if (!preg_match_all('/<(input|select|textarea|div|span)\b[^>]*\baria-invalid\s*=\s*(?:"([^"]*)"|\'([^\']*)\')[^>]*>/i', $full, $matches, PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($matches[0] as $index => $match) {
            $tag = $match[0];
            $offset = $match[1];
            $tagName = strtolower($matches[1][$index][0]);
            $ariaInvalid = strtolower(trim($this->firstMatch([
                1 => $matches[2][$index][0],
                2 => $matches[3][$index][0],
            ], 1, 2)));

            if (!in_array($ariaInvalid, ['true', 'grammar', 'spelling'], true)) {
                continue;
            }

            if (!$this->isSupportedField($tagName, $tag)) {
                continue;
            }
            if ($this->hasNonEmptyReference($tag, 'aria-describedby')) {
                continue;
            }
            if ($this->hasNonEmptyReference($tag, 'aria-errormessage')) {
                continue;
            }

            $line = 1 + substr_count(substr($full, 0, $offset), "\n");
            $fakeToken = $this->fakeTokenForLine($tokens, $line, $tag);

            $emit('Invalid form fields should reference help or error text via aria-describedby or aria-errormessage.', $fakeToken, 'InvalidFieldErrorMessage.MissingReference');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    private function isSupportedField(string $tagName, string $tag): bool
    {
        if (in_array($tagName, ['input', 'select', 'textarea'], true)) {
            return true;
        }

        if (preg_match('/\brole\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $tag, $roleMatch)) {
            $role = strtolower(trim($this->firstMatch($roleMatch, 1, 2)));

            return in_array($role, self::CUSTOM_FIELD_ROLES, true);
        }

        return false;
    }

    private function hasNonEmptyReference(string $tag, string $attribute): bool
    {
        if (!preg_match('/\b'.preg_quote($attribute, '/').'\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $tag, $match)) {
            return false;
        }

        return '' !== trim($this->firstMatch($match, 1, 2));
    }

    private function fakeTokenForLine(Tokens $tokens, int $line, string $value): Token
    {
        $token = $tokens->get(0);

        return new Token(
            $token->getType(),
            $line,
            1,
            $token->getFilename(),
            $value
        );
    }
}
