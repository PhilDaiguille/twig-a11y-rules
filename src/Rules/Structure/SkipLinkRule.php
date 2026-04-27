<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class SkipLinkRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        // Only perform a single page-level scan (when invoked on the first
        // token) to detect absence of skip links. This avoids emitting the
        // same violation multiple times.
        if (0 !== $tokenIndex) {
            return;
        }

        $content = $this->getFullContent($tokens);

        if (preg_match('/href\s*=\s*["\"]#([^"\']+)["\"][^>]*>.*?skip/i', $content)) {
            return;
        }

        if (preg_match('/href\s*=\s*["\"]#(main|content)["\"][^>]*>/i', $content)) {
            return;
        }

        $emit('Page should include a skip link to bypass navigation', $token, 'SkipLink.Missing');
    }
}
