<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AriaLabelRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        // landmarks: role=main, banner, navigation, complementary, contentinfo
        if (!preg_match('/role\s*=\s*(?:"|\')(main|banner|navigation|complementary|contentinfo)(?:"|\')/i', $value)) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '>');

        // aria-label present and non-empty - OK
        if (preg_match('/aria-label\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $tag, $m) && '' !== trim($m[1])) {
            return;
        }

        // aria-labelledby present and non-empty - also acceptable per WCAG
        if (preg_match('/aria-labelledby\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $tag, $m) && '' !== trim($m[1])) {
            return;
        }

        $emit(
            'Landmark elements should have a non-empty aria-label.',
            $token,
            'AriaLabel.MissingOrEmpty'
        );
    }
}
