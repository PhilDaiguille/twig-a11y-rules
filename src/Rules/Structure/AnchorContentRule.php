<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AnchorContentRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Guard against out-of-range token indexes using the Tokens API.
        if (!$tokens->has($tokenIndex)) {
            return;
        }

        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<a')) {
            return;
        }

        // anchor bodies can be large; allow a larger search window
        $full = $this->collectUntil($tokenIndex, $tokens, '/<\/a>/i', 200);

        if (preg_match('/<a[^>]*>(.*?)<\/a>/is', $full, $m)) {
            $inner = $m[1];
            $textOnly = trim(strip_tags($inner));

            $opening = '';
            if (preg_match('/<a[^>]*>/i', $full, $o)) {
                $opening = $o[0];
            }

            if ('' === $textOnly
                && !preg_match('/\baria-label\s*=\s*("|\')/i', $opening)
                && !preg_match('/\btitle\s*=\s*("|\')/i', $opening)
            ) {
                // Axe-core rule reference: link-name
                $emit('Anchor element without accessible name (axe-core: link-name) should have an aria-label or title.', $token, 'AnchorContent.Warning.LinkName');
            }
        }
    }

    protected function emitsWarnings(): bool
    {
        return true;
    }
}
