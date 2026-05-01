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

        // Look for at least one <h1> element with non-empty content (plain text
        // or child elements like <span>). strip_tags() is used on the captured
        // inner content to ensure there is actual visible text and not just markup.
        if (!preg_match('/<h1\b[^>]*>(.*?)<\/h1>/is', $content, $m) || '' === trim(strip_tags($m[1]))) {
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
}
