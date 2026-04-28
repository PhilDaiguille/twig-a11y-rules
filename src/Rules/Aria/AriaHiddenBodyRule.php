<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Tokens;

final class AriaHiddenBodyRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        // Only apply to full pages
        if (!str_contains(strtoupper($full), '<!DOCTYPE') && !str_contains($full, '<body')) {
            return;
        }

        if (preg_match('/<body[^>]*aria-hidden\s*=\s*(?:"|\')true(?:"|\')/i', $full)) {
            $first = $tokens->get(0);
            $emit('Do not set aria-hidden="true" on the <body> element.', $first, 'AriaHiddenBody.HiddenOnBody');
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
