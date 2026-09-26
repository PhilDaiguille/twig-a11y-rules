<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 4.1.1 — the summary attribute on <table> is obsolete in HTML5.
 * Use <caption> instead to provide a visible description of the table.
 */
final class SummaryAttributeObsoleteRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        if (!str_contains($value, '<table')) {
            return;
        }

        $opening = $this->collectOpeningTag($tokenIndex, $tokens, 'table');
        if ('' === $opening) {
            return;
        }

        if (!preg_match('/\bsummary\s*=/i', $opening)) {
            return;
        }

        $id = 'ObsoleteSummary';

        $emit(
            'The summary attribute on <table> is obsolete in HTML5. Use <caption> to describe the table (WCAG 4.1.1).',
            $token,
            $id
        );
    }
}
