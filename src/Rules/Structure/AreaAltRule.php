<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AreaAltRule extends AbstractA11yRule
{
    /**
     * Deduplicate reports for the same tag across token evaluations.
     *
     * @var array<string,bool>
     */
    private array $seenTagHashes = [];

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $token = $tokens->get($tokenIndex);

        // Token::isMatching is available on Token objects and narrows the
        // check; directly call it.
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

        // Only act on things that look like an <area tag.
        if (!preg_match('/<\s*area\b/i', $fullTag)) {
            return;
        }

        // Deduplicate by hashing the normalized tag text.
        $normalized = preg_replace('/\s+/', ' ', trim($fullTag));
        $tagKey = md5((string) $normalized);
        if (isset($this->seenTagHashes[$tagKey])) {
            return;
        }

        $this->seenTagHashes[$tagKey] = true;

        // Extract attributes portion
        if (!preg_match('/<area\b([^>]*)>/i', $fullTag, $m)) {
            return;
        }

        // preg_match succeeded, so capture index 1 exists.
        $attrs = $m[1];

        if (!preg_match('/\balt\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $am)) {
            $emit('Missing alt attribute on <area> tag.', $token, 'AreaAlt.MissingAlt');

            return;
        }

        $alt = $this->firstMatch($am, 1, 2);

        if ('' === $alt) {
            $hasDecorativeRole = preg_match('/\brole\s*=\s*(["\'])(?:presentation|none)\1/i', $attrs);
            if (!$hasDecorativeRole) {
                $emit('Empty alt on <area> requires role="presentation" or role="none".', $token, 'AreaAlt.EmptyAlt');
            }
        }
    }
}
