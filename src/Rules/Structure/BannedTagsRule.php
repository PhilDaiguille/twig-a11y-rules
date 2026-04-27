<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class BannedTagsRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = strtolower($token->getValue());
        if (str_contains($value, '<marquee') || str_contains($value, '<blink')) {
            $emit('Banned tag used (e.g. <marquee> or <blink>).', $token, 'BannedTags.Used');
        }
    }
}
