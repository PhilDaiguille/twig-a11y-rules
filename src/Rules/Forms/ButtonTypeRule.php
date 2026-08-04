<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ButtonTypeRule extends AbstractA11yRule
{
    /**
     * Number of <form> elements currently open at this point of the token stream.
     *
     * ponytail: a plain counter, not a real tag stack — tokens are visited in
     * source order, which is enough to know whether we are inside a form.
     */
    private int $formDepth = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        $this->formDepth += preg_match_all('/<\s*form\b/i', $value);
        $this->formDepth -= preg_match_all('/<\s*\/\s*form\s*>/i', $value);
        $this->formDepth = max(0, $this->formDepth);

        // Outside a form a missing type is harmless: the implicit "submit"
        // default only causes a surprise submission within a form.
        if (0 === $this->formDepth) {
            return;
        }

        if (!str_contains($value, '<button')) {
            return;
        }

        $tag = $this->collectOpeningTag($tokenIndex, $tokens, 'button', 100);

        if ('' === $tag) {
            return;
        }

        if (preg_match('/\btype\s*=\s*["\'](?:button|submit|reset)["\']/i', $tag)) {
            return;
        }

        $emit('Button inside a form should declare an explicit type attribute.', $token, 'ButtonType.MissingType');
    }

    #[\Override]
    protected function evaluateStart(Tokens $tokens): void
    {
        $this->formDepth = 0;
    }
}
