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

    public function __construct()
    {
        parent::__construct(emitAsWarning: true);
    }

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // This rule inspects many inline tokens but should still run for
        // each token that may contain an <img fragment. Use the per-token
        // guard helper to allow page-level short-circuits where applicable.
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        // Reset deduplication hashes at the start of each new file so that
        // identical <img> tags in different files are not silently suppressed.
        if (0 === $tokenIndex) {
            $this->seenTagHashes = [];
        }

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

        if (!str_contains($fullTag, '>')) {
            // No complete tag found in 200 tokens, give up.
            return;
        }

        // Only act on things that look like an <img tag.
        if (!preg_match('/<\s*img\b/i', $fullTag)) {
            return;
        }

        // Deduplicate by hashing the normalized tag text. Many tokens may
        // contain fragments of the same tag; we only want a single report.
        $normalized = preg_replace('/\s+/', ' ', trim($fullTag));
        $tagKey = md5((string) $normalized);
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
            $attrValue = $this->firstMatch($matches, 1, 2, 3);

            // Use the string-based helper on the extracted attribute value.
            // containsTwigExpressions() uses str_contains which is safe and
            // reliable for values already assembled from the token stream.
            $hasTwig = $this->containsTwigExpressions($attrValue);

            if ($hasTwig) {
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
}
