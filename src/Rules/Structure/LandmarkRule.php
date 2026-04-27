<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class LandmarkRule extends AbstractA11yRule
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

        $value = $token->getValue();

        // Detect presence of main landmark or role="main"
        if (str_contains($value, '<main') || str_contains($value, 'role="main"') || str_contains($value, "role='main'")) {
            return;
        }

        // This rule is page-level. Only evaluate once per file (at tokenIndex 0)
        // and only if the content looks like a full HTML page (contains
        // a <body> or a <!DOCTYPE). This avoids flagging fragments/partials.
        if (0 !== $tokenIndex) {
            return;
        }

        $full = $this->getFullContent($tokens);

        // If this looks like a fragment (no body/doctype), skip evaluation
        if (!str_contains($full, '<body') && !str_contains(strtoupper($full), '<!DOCTYPE')) {
            return;
        }

        // Scan the full content for a main landmark or role="main"
        if (str_contains($full, '<main') || preg_match('/role\s*=\s*["\']main["\']/i', $full)) {
            return;
        }

        $first = $tokens->get(0);
        $emit('Page should include a main landmark', $first, 'Landmark.MissingMain');
    }
}
