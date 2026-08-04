<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Forms\ButtonTypeRuleTest;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * @see ButtonTypeRuleTest
 * @see ButtonTypeRuleTest
 * @see ButtonTypeRuleTest
 * @see ButtonTypeRuleTest
 * @see ButtonTypeRuleTest
 * @see ButtonTypeRuleTest
 */
final class ButtonTypeRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<button')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '>', 100);

        if (preg_match('/\btype\s*=\s*["\'](?:button|submit|reset)["\']/i', $tag)) {
            return;
        }

        $emit('Button inside a form should declare an explicit type attribute.', $token, 'ButtonType.MissingType');
    }
}
