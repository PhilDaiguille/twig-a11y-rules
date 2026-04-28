<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Checks that all <frame> elements have a non-empty title attribute.
 *
 * Axe-core rule: frame-title (Critical)
 * WCAG 2.4.1 — Bypass Blocks; 4.1.2 — Name, Role, Value
 */
final class FrameTitleRule extends AbstractA11yRule
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

        $value = strtolower($token->getValue());
        if (!str_contains($value, '<frame')) {
            return;
        }

        // Collect ahead to get the full tag and distinguish <frame> from <frameset>
        $tag = $this->collectUntil($tokenIndex, $tokens, '>');

        // Only target <frame>, not <frameset> or <iframe>
        if (!preg_match('/<frame\b/i', $tag) || preg_match('/<frameset\b/i', $tag) || preg_match('/<iframe\b/i', $tag)) {
            return;
        }

        if (!preg_match('/\btitle\s*=\s*(?:"|\')([^"\']*)(?:"|\')/i', $tag, $m) || '' === trim($m[1])) {
            ++$this->idx;
            $id = 'FrameTitle.Missing';
            if ($this->idx > 1) {
                $id .= '#'.$this->idx;
            }

            $emit('Frame element must have a non-empty title attribute.', $token, $id);
        }
    }
}
