<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Tokens;

final class PageHeadingOneRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $content = $this->getFullContent($tokens);

        // look for at least one <h1> element with non-empty content
        if (!preg_match('/<h1\b[^>]*>\s*([^<]+?)\s*<\/h1>/is', $content)) {
            $tokenRef = $tokens->get(0);
            $emit('Document should include at least one non-empty <h1> heading.', $tokenRef, 'PageHeadingOne.Missing');
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    /**
     * @return TemplateKind[]
     */
    protected function supportedKinds(): array
    {
        return [TemplateKind::FullPage];
    }

    protected function shouldSkipByTokenIndex(int $tokenIndex): bool
    {
        return $this->evaluateOncePerFile() && 0 !== $tokenIndex;
    }
}
