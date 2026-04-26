<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ImgAltRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<img')) {
            return;
        }

        $fullTag = $this->collectTag($tokenIndex, $tokens);

        if (!str_contains($fullTag, '>')) {
            return;
        }

        if (!preg_match('/\balt\s*=/i', $fullTag)) {
            $emit('Missing alt attribute on <img> tag.', $token, 'ImgAlt.MissingAlt');

            return;
        }

        if (preg_match('/\balt\s*=\s*(["\'])\1/i', $fullTag)) {
            $hasDecorativeRole = preg_match(
                '/\brole\s*=\s*(["\'])(?:presentation|none)\1/i',
                $fullTag
            );
            if (!$hasDecorativeRole) {
                $emit('Empty alt on <img> requires role="presentation" or role="none".', $token, 'ImgAlt.EmptyAlt');
            }
        }
    }

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $emit = function (string $message, Token $token, ?string $id = null): void {
            $this->addError($message, $token, $id);
        };

        $this->evaluate($tokens, $tokenIndex, $emit);
    }

    private function collectTag(int $tokenIndex, Tokens $tokens): string
    {
        $tag = '';
        $i = $tokenIndex;
        $maxLookAhead = 50;

        // Tokens::get() always returns a Token instance from the library we use,
        // so avoid nullable checks that PHPStan will flag. Coerce values to string
        // when concatenating to avoid unnecessary null-coalescing.
        $end = $tokenIndex + $maxLookAhead;
        while ($i < $end) {
            $t = $tokens->get($i);
            $value = $t->getValue();
            $tag .= $value;
            if ($t->isMatching(Token::TEXT_TYPE) && str_contains($value, '>') && $i !== $tokenIndex) {
                break;
            }
            ++$i;
        }

        return $tag;
    }
}
