<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class LangAttributeRule extends AbstractA11yRule
{
    private int $idx = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 === $tokenIndex) {
            $this->idx = 0;
        }

        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<html')) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '>');

        if (!preg_match('/\blang\s*=\s*("|\')([^"\']*)("|\')/i', $opening, $m) || '' === trim($m[2])) {
            ++$this->idx;
            $id = 'LangAttribute.MissingLang';
            if ($this->idx > 1) {
                $id .= '#'.$this->idx;
            }

            $emit('The <html> element should have a non-empty lang attribute.', $token, $id);
        }
    }

    /**
     * @return TemplateKind[]
     */
    protected function supportedKinds(): array
    {
        return [TemplateKind::FullPage];
    }
}
