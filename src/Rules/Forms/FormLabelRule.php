<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Checks that every <label> element is well-formed: it must either have a
 * non-empty `for` attribute pointing to a visible field, or wrap a form
 * control directly.
 *
 * This rule targets the <label> element itself — the inverse of
 * AbstractFormFieldLabelRule which checks fields for their labels.
 * The two concerns are distinct and cannot share the same abstraction.
 */
final class FormLabelRule extends AbstractA11yRule
{
    private int $idx = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<label')) {
            return;
        }

        $opening = $this->collectOpeningTag($tokenIndex, $tokens, 'label');
        if ('' === $opening) {
            return;
        }

        $label = $this->getLabelScope($tokens, $tokenIndex);
        $inner = substr($label, strlen($opening));

        $forId = 1 === preg_match('/\sfor\s*=\s*(["\'])(.+?)\1/is', $opening, $m) ? $m[2] : '';

        $hasContent = '' !== trim(strip_tags($inner));
        $wrapsControl = 1 === preg_match('/<(?:input|select|textarea)\b/i', $inner);

        if ('' !== $forId && $hasContent) {
            return;
        }

        if ($wrapsControl) {
            return;
        }

        ++$this->idx;
        $id = 'FormLabel.InvalidLabel';
        if ($this->idx > 1) {
            $id .= '#'.$this->idx;
        }

        $emit(
            '<label> must have a for attribute or wrap the related element.',
            $token,
            $id
        );
    }

    protected function evaluateStart(Tokens $tokens): void
    {
        $this->idx = 0;
    }

    private function getLabelScope(Tokens $tokens, int $start): string
    {
        $scope = '';
        for ($i = $start; $tokens->has($i); ++$i) {
            $scope .= $tokens->get($i)->getValue();
            if (false !== stripos($scope, '</label>')) {
                break;
            }
        }

        $from = stripos($scope, '<label');
        $to = stripos($scope, '</label>', (int) $from);

        return false === $from || false === $to ? '' : substr($scope, $from, $to - $from);
    }
}
