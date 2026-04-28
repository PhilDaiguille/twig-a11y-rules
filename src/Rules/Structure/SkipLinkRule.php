<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class SkipLinkRule extends AbstractA11yRule
{
    private int $idx = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 === $tokenIndex) {
            $this->idx = 0;
        }

        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        // Only perform the page-level checks once per file; AbstractA11yRule
        // will enforce the evaluateOncePerFile behaviour if needed. Here we
        // simply run the detection logic and emit on the current token.
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

        $first = $tokens->get(0);

        ++$this->idx;
        $id = 'SkipLink.Missing';
        if ($this->idx > 1) {
            $id .= '#'.$this->idx;
        }

        $emit('Page should include a skip link to bypass navigation', $first, $id);
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
