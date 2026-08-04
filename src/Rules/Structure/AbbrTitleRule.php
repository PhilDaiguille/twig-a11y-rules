<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * RGAA 9.4 / WCAG 3.1.4 — every <abbr> must have a non-empty title attribute
 * providing the expansion of the abbreviation.
 */
final class AbbrTitleRule extends AbstractA11yRule
{
    private int $idx = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        if (!str_contains($value, '<abbr')) {
            return;
        }

        $opening = $this->collectOpeningTag($tokenIndex, $tokens, 'abbr');
        if ('' === $opening) {
            return;
        }

        if (preg_match('/\btitle\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $opening, $m)) {
            $titleValue = '' !== $m[1] ? $m[1] : ($m[2] ?? '');
            if ('' !== trim($titleValue)) {
                return;
            }
        }

        ++$this->idx;
        $id = 'MissingTitle';
        if ($this->idx > 1) {
            $id .= '#'.$this->idx;
        }

        $emit(
            '<abbr> element must have a non-empty title attribute providing the expansion (RGAA 9.4).',
            $token,
            $id
        );
    }

    protected function evaluateStart(Tokens $tokens): void
    {
        $this->idx = 0;
    }
}
