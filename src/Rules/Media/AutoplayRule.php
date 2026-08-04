<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Media\AutoplayRuleTest;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * @see AutoplayRuleTest
 * @see AutoplayRuleTest
 * @see AutoplayRuleTest
 * @see AutoplayRuleTest
 * @see AutoplayRuleTest
 * @see AutoplayRuleTest
 */
final class AutoplayRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = strtolower($token->getValue());
        if (!str_contains($value, '<video') && !str_contains($value, '<audio')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '>');

        if (preg_match('/autoplay\b/i', $tag) && !preg_match('/\bmuted\b/i', $tag)) {
            $emit('Autoplaying media should be muted.', $token, 'Autoplay.NotMuted');
        }
    }
}
