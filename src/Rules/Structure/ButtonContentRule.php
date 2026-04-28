<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ButtonContentRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<button')) {
            return;
        }

        $full = $this->collectUntil($tokenIndex, $tokens, '/<\/button>/i', 200);

        // If inner text stripped from tags is empty and no aria-label attribute
        if (preg_match('/<button[^>]*>(.*?)<\/button>/is', $full, $m)) {
            $inner = $m[1];
            $textOnly = trim(strip_tags($inner));

            $opening = '';
            if (preg_match('/<button[^>]*>/i', $full, $o)) {
                $opening = $o[0];
            }

            if ('' === $textOnly && !preg_match('/\baria-label\s*=\s*("|\')/i', $opening)) {
                /** @var int $idx */
                static $idx = 0;
                ++$idx;
                $id = 'ButtonContent.MissingContent';
                if ($idx > 1) {
                    $id .= '#'.$idx;
                }

                $emit('Button element without textual content must have an aria-label.', $token, $id);
            }
        }
    }
}
