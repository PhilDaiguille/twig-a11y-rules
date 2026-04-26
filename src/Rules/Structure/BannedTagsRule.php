<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class BannedTagsRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = strtolower($token->getValue());
        if (str_contains($value, '<marquee') || str_contains($value, '<blink')) {
            $this->addError(
                'Banned tag used (e.g. <marquee> or <blink>).',
                $token,
                'BannedTags.Used'
            );
        }
    }
}
