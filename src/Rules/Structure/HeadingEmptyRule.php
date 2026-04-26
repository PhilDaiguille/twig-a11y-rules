<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class HeadingEmptyRule extends AbstractRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $full = '';
        foreach ($tokens->toArray() as $t) {
            $full .= $t->getValue();
        }

        preg_match_all('/<(h[1-6])[^>]*>(.*?)<\/\1>/is', $full, $m, PREG_SET_ORDER);
        foreach ($m as $set) {
            $content = trim(strip_tags($set[2]));
            if ($content === '') {
                $this->addError('Heading element should not be empty.', $token, 'HeadingEmpty.Empty');
                return;
            }
        }
    }
}
