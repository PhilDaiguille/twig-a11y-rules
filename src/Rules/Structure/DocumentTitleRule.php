<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Tokens;

final class DocumentTitleRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // only runs at tokenIndex 0 because evaluateOncePerFile = true
        $content = $this->getFullContent($tokens);

        // Look for a <title> tag with non-empty content inside <head>
        if (!preg_match('/<head[\s>].*?<title\s*>\s*([^<\n]+?)\s*<\/title>/is', $content)) {
            $tokenRef = $tokens->get(0);
            $emit('Document should include a non-empty <title> element.', $tokenRef, 'DocumentTitle.Missing');
        }
    }

    // page-level rule
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
}
