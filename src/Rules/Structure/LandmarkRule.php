<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class LandmarkRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // This is a page-level rule: defer to evaluateOncePerFile helper so
        // the rule only runs once per file. For backwards compatibility we
        // keep the per-token guard via shouldSkipByTokenIndex().
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        // Detect presence of main landmark or role="main"
        if (str_contains($value, '<main') || str_contains($value, 'role="main"') || str_contains($value, "role='main'")) {
            return;
        }

        // The AbstractA11yRule helper provides the once-per-file behaviour; here
        // we just need to check the full-page heuristics and emit if missing.
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
