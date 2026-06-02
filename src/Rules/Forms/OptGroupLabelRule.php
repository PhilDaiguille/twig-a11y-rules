<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 1.3.1 — <optgroup> inside a <select> must have a non-empty label attribute
 * so that option groups are announced to assistive technologies.
 */
final class OptGroupLabelRule extends AbstractA11yRule
{
    private int $idx = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        if (!str_contains($value, '<optgroup')) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '>');

        if (preg_match('/\blabel\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $opening, $m)) {
            $labelValue = '' !== $m[1] ? $m[1] : ($m[2] ?? '');
            if ('' !== trim($labelValue)) {
                return;
            }
        }

        ++$this->idx;
        $id = 'MissingLabel';
        if ($this->idx > 1) {
            $id .= '#'.$this->idx;
        }

        $emit(
            '<optgroup> must have a non-empty label attribute to identify the group (WCAG 1.3.1).',
            $token,
            $id
        );
    }

    protected function evaluateStart(Tokens $tokens): void
    {
        $this->idx = 0;
    }
}
