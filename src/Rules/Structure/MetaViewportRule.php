<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class MetaViewportRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Guard so we only perform the full-file scan once by running on the
        // first token index. Using an instance-scoped scanned flag caused the
        // rule to silently skip later files when the same instance was reused.
        if (0 !== $tokenIndex) {
            return;
        }

        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        $fullLower = strtolower($full);
        if (!str_contains($fullLower, 'name="viewport"') && !str_contains($fullLower, "name='viewport'")) {
            return;
        }

        if (preg_match('/user-scalable\s*=\s*no/i', $fullLower)) {
            $token = $tokens->get(0);
            $emit('Avoid using user-scalable=no in the viewport meta.', $token, 'MetaViewport.UserScalable');
        }
    }
}
