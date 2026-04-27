<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class SkipLinkRule extends AbstractA11yRule
{
    protected function supportedKinds(): array
    {
        return [\TwigA11y\Template\TemplateKind::FullPage];
    }

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        // Page-level rule: only run once (tokenIndex 0) and only for full
        // pages (containing <body> or <!DOCTYPE). This avoids reporting on
        // partials/components.
        if (0 !== $tokenIndex) {
            return;
        }

        $content = $this->getFullContent($tokens);

        if (!str_contains($content, '<body') && !str_contains(strtoupper($content), '<!DOCTYPE')) {
            return;
        }

        if (preg_match('/href\s*=\s*["\']#([^"\']+)["\'][^>]*>.*?skip/i', $content)) {
            return;
        }

        if (preg_match('/href\s*=\s*["\']#(main|content)["\'][^>]*>/i', $content)) {
            return;
        }

        $emit('Page should include a skip link to bypass navigation', $token, 'SkipLink.Missing');
    }
}
