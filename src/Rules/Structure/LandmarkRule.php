<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class LandmarkRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        // Detect presence of main landmark or role="main"
        if (str_contains($value, '<main') || str_contains($value, 'role="main"') || str_contains($value, "role='main'")) {
            return;
        }

        // If we encounter the end of head/body start, and haven't seen
        // a main landmark previously, emit a missing landmark warning.
        if (str_contains($value, '<body') || str_contains($value, '</head>')) {
            // Simple approach: scan a small window ahead to see if a main is present
            $look = $this->collectUntil($tokenIndex, $tokens, '<main', 200);
            if (!str_contains($look, '<main') && !preg_match('/role\s*=\s*["\']main["\']/i', $look)) {
                // Emit at the start of the file for consistency with other
                // page-level rules (tests expect line 1:1 identifiers).
                $first = $tokens->get(0);
                $emit('Page should include a main landmark', $first, 'Landmark.MissingMain');
            }
        }
    }
}
