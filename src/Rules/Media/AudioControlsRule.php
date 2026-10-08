<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 1.2.1 A — <audio> elements must have a controls attribute so that
 * keyboard users can operate them without relying on custom scripts.
 */
final class AudioControlsRule extends AbstractA11yRule
{
    #[\Override]
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

        $opening = $this->collectOpeningTag($tokenIndex, $tokens, 'audio');
        if ('' === $opening) {
            return;
        }

        // Ignore inline elements that are purely decorative background audio via autoplay+muted
        // (those are caught by AutoplayRule / NoAutoplayAudioRule).
        if (preg_match('/\bcontrols\b/i', $opening)) {
            return;
        }

        $id = 'MissingControls';

        $emit(
            '<audio> element must have a controls attribute to be operable by keyboard users (WCAG 1.2.1).',
            $token,
            $id,
        );
    }
}
