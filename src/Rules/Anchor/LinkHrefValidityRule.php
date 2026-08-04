<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Anchor;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Anchor\LinkHrefValidityRuleTest;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * @see LinkHrefValidityRuleTest
 * @see LinkHrefValidityRuleTest
 * @see LinkHrefValidityRuleTest
 * @see LinkHrefValidityRuleTest
 * @see LinkHrefValidityRuleTest
 * @see LinkHrefValidityRuleTest
 */
final class LinkHrefValidityRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<a')) {
            return;
        }

        $tag = $this->collectUntil($tokenIndex, $tokens, '>', 100);

        if (!preg_match('/<a\b/i', $tag)) {
            return;
        }

        if (preg_match('/\brole\s*=\s*["\']button["\']/i', $tag)) {
            return;
        }

        if ($this->containsTwigExpressions($tag)) {
            return;
        }

        if (!preg_match('/\bhref\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $tag, $hrefMatch)) {
            $emit('Anchor elements should include a valid href attribute.', $token, 'LinkHref.MissingHref');

            return;
        }

        $href = $this->firstMatch($hrefMatch, 1, 2);
        $normalized = strtolower(trim($href));

        if ('' === $normalized) {
            $emit('Anchor elements should not use an empty href attribute.', $token, 'LinkHref.EmptyHref');

            return;
        }

        if ('#' === $normalized || 'javascript:void(0)' === $normalized || 'javascript:void(0);' === $normalized) {
            $emit('Anchor elements should use a real destination href instead of placeholder links.', $token, 'LinkHref.PlaceholderHref');
        }
    }
}
