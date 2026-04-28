<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class MetaViewportRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Page-level rule: use the new helper to skip non-zero token indexes.
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
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

    /**
     * @return TemplateKind[]
     */
    protected function supportedKinds(): array
    {
        return [TemplateKind::FullPage];
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
