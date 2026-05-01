<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Tokens;

final class MetaViewportRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Page-level rule: use the new helper to skip non-zero token indexes.
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        $fullLower = strtolower($full);
        if (!str_contains($fullLower, 'name="viewport"') && !str_contains($fullLower, "name='viewport'")) {
            return;
        }

        if (preg_match('/user-scalable\s*=\s*no/i', $fullLower)) {
            $emit('Avoid using user-scalable=no in the viewport meta.', $tokens->get(0), 'MetaViewport.UserScalable');
        }

        // WCAG 1.4.4: maximum-scale must be >= 2 to allow users to zoom text.
        if (preg_match('/maximum-scale\s*=\s*([0-9]*\.?[0-9]+)/i', $fullLower, $m) && (float) $m[1] < 2.0) {
            $emit('Avoid setting maximum-scale below 2 in the viewport meta (WCAG 1.4.4).', $tokens->get(0), 'MetaViewport.MaximumScale');
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
