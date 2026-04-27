<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ImgAltRule extends AbstractA11yRule
{
    /**
     * Deduplicate reports for the same tag across token evaluations.
     *
     * @var array<string,bool>
     */
    private array $seenTagHashes = [];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        // Quick check: ignore tokens that clearly don't contain a '<'.
        if (!str_contains($value, '<')) {
            return;
        }

        // Try to collect a full tag around this token. Use a larger limit to
        // handle long attributes and Twig interpolations.
        $fullTag = $this->collectTag($tokenIndex, $tokens, 200);

        // If collectTag failed to find a closing '>', try scanning adjacent
        // tokens forward to assemble a candidate.
        if (!str_contains($fullTag, '>')) {
            $collected = $fullTag;
            $i = $tokenIndex + 1;
            $limit = $tokenIndex + 200;
            while ($i <= $limit && $tokens->has($i) && !str_contains($collected, '>')) {
                $collected .= $tokens->get($i)->getValue();
                ++$i;
            }

            $fullTag = $collected;
        }

        if (!str_contains($fullTag, '>')) {
            // No complete tag found, give up.
            return;
        }

        // Only act on things that look like an <img tag.
        if (!preg_match('/<\s*img\b/i', $fullTag)) {
            return;
        }

        // Deduplicate by hashing the normalized tag text. Many tokens may
        // contain fragments of the same tag; we only want a single report.
        $normalized = preg_replace('/\s+/', ' ', trim($fullTag));
        $tagKey = md5($normalized);
        if (isset($this->seenTagHashes[$tagKey])) {
            return;
        }
        $this->seenTagHashes[$tagKey] = true;

        if (!preg_match('/\balt\s*=/i', $fullTag)) {
            $emit('Missing alt attribute on <img> tag.', $token, 'ImgAlt.MissingAlt');

            return;
        }

        // Extract alt attribute value if present.
        if (preg_match('/\balt\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/is', $fullTag, $matches)) {
            $attrValue = $matches[1] ?? $matches[2] ?? ($matches[3] ?? '');

            if ($this->containsTwigExpressions($attrValue)) {
                // Can't decide statically. Emit a warning to prompt manual check.
                // Use warnings (non-fatal) by emitting via the emitter — for that
                // we make this rule declare it emits warnings by overriding
                // emitsWarnings() (below).
                $emit('Alt attribute contains template expression; verify it is non-empty at runtime.', $token, 'ImgAlt.DynamicAlt');

                return;
            }
        }

        if (preg_match('/\balt\s*=\s*(["\'])\1/i', $fullTag)) {
            $hasDecorativeRole = preg_match(
                '/\brole\s*=\s*(["\'])(?:presentation|none)\1/i',
                $fullTag
            );
            if (!$hasDecorativeRole) {
                $emit('Empty alt on <img> requires role="presentation" or role="none".', $token, 'ImgAlt.EmptyAlt');
            }
        }
    }

    protected function emitsWarnings(): bool
    {
        return true;
    }
}
