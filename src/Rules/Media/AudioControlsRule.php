<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Media\AudioControlsRuleTest;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 1.2.1 A — <audio> elements must have a controls attribute so that
 * keyboard users can operate them without relying on custom scripts.
 *
 * @see AudioControlsRuleTest
 * @see AudioControlsRuleTest
 * @see AudioControlsRuleTest
 * @see AudioControlsRuleTest
 * @see AudioControlsRuleTest
 * @see AudioControlsRuleTest
 */
final class AudioControlsRule extends AbstractA11yRule
{
    private int $idx = 0;

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        if (!str_contains($value, '<audio')) {
            return;
        }

        $opening = $this->collectUntil($tokenIndex, $tokens, '>');

        // Ignore inline elements that are purely decorative background audio via autoplay+muted
        // (those are caught by AutoplayRule / NoAutoplayAudioRule).
        if (preg_match('/\bcontrols\b/i', $opening)) {
            return;
        }

        ++$this->idx;
        $id = 'MissingControls';
        if ($this->idx > 1) {
            $id .= '#'.$this->idx;
        }

        $emit(
            '<audio> element must have a controls attribute to be operable by keyboard users (WCAG 1.2.1).',
            $token,
            $id
        );
    }

    protected function evaluateStart(Tokens $tokens): void
    {
        $this->idx = 0;
    }
}
