<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

trait TokenCollectorTrait
{
    /**
     * Collect token values starting at $tokenIndex until $endPattern matches or
     * $limit tokens have been consumed. $limit is treated as a token-count
     * offset from the starting index (default 200).
     *
     * Every token in the range is visited sequentially so that whitespace and
     * EOL tokens between HTML attributes are included in the collected string.
     * Use tokenRangeContainsTwig() when you need to detect Twig expression
     * tokens in a range without assembling the full string.
     */
    protected function collectUntil(int $tokenIndex, Tokens $tokens, string $endPattern, int $limit = 200): string
    {
        $collected = '';
        $end = $tokenIndex + $limit;

        for ($i = $tokenIndex; $i <= $end && $tokens->has($i); ++$i) {
            $collected .= $tokens->get($i)->getValue();

            if (str_starts_with($endPattern, '/')) {
                if ($this->safePregMatch($endPattern, $collected)) {
                    break;
                }
            } elseif (str_contains($collected, $endPattern)) {
                break;
            }
        }

        return $collected;
    }

    /**
     * Collect tokens from $tokenIndex up to and including the closing '>'.
     * Uses collectUntil() which internally leverages Tokens::findNext().
     */
    protected function collectTag(int $tokenIndex, Tokens $tokens, int $limit = 50): string
    {
        return $this->collectUntil($tokenIndex, $tokens, '>', $limit);
    }

    /**
     * Collect the opening tag of $tagName that starts inside the token at
     * $tokenIndex, continuing into the following tokens until its closing '>'.
     *
     * Prefer this over collectTag() when the tag can follow another one inside
     * the same text token: the tokenizer emits values such as "<li><a", and
     * collectTag() would stop on the '>' of the <li>, returning an attribute-less
     * fragment. Anchoring on the tag name avoids that.
     *
     * Returns '' when the token does not open such a tag, so that callers keep
     * reporting once per tag instead of once per token that happens to precede
     * one.
     */
    protected function collectOpeningTag(int $tokenIndex, Tokens $tokens, string $tagName, int $limit = 50): string
    {
        $startLength = \strlen($tokens->get($tokenIndex)->getValue());

        $collected = '';
        $end = $tokenIndex + $limit;

        for ($i = $tokenIndex; $i <= $end && $tokens->has($i); ++$i) {
            $collected .= $tokens->get($i)->getValue();
        }

        if (!preg_match('/<\s*'.preg_quote($tagName, '/').'\b[^>]*>/i', $collected, $m, \PREG_OFFSET_CAPTURE)) {
            return '';
        }

        [$tag, $offset] = $m[0];

        // The tag must open in the current token; otherwise it belongs to a
        // later token, which will be visited on its own.
        return $offset < $startLength ? $tag : '';
    }

    /**
     * Collect the opening tag that encloses the token at $tokenIndex.
     *
     * Use this when a rule is triggered by an *attribute* rather than by the
     * tag name: the tokenizer splits on whitespace, so `role="main"` and the
     * `<div` that carries it are different tokens, and collecting forward only
     * would miss every attribute written before the trigger one.
     */
    protected function collectEnclosingTag(int $tokenIndex, Tokens $tokens, int $limit = 50): string
    {
        $start = $tokenIndex;
        $lowest = max(0, $tokenIndex - $limit);

        for ($i = $tokenIndex; $i >= $lowest; --$i) {
            if (!$tokens->has($i)) {
                continue;
            }

            if (preg_match('/<\s*[a-z][a-z0-9-]*/i', $tokens->get($i)->getValue())) {
                $start = $i;

                break;
            }
        }

        return $this->collectUntil($start, $tokens, '>', $limit * 2);
    }

    /**
     * Safe wrapper around preg_match that avoids silencing errors with @ and
     * returns a boolean result. If the pattern is invalid, false is returned.
     */
    protected function safePregMatch(string $pattern, string $subject): bool
    {
        try {
            $res = preg_match($pattern, $subject);
        } catch (\Throwable) {
            return false;
        }

        if (false === $res) {
            return false;
        }

        return (bool) $res;
    }

    /**
     * Detect Twig expressions in a plain string (fallback for rules that
     * operate on already-assembled content via getFullContent()).
     *
     * Prefer tokenRangeContainsTwig() when you have access to the Tokens
     * object and index range — it is more reliable because it checks actual
     * token types rather than raw string patterns.
     */
    protected function containsTwigExpressions(string $s): bool
    {
        return str_contains($s, '{{') || str_contains($s, '{%');
    }

    /**
     * Returns true if any token in [$start, $end) is a Twig expression or
     * block start token (VAR_START_TYPE or BLOCK_START_TYPE).
     *
     * This is the preferred alternative to containsTwigExpressions() when you
     * have direct access to the Tokens object, because it relies on the
     * tokenizer's own type information rather than string pattern matching.
     *
     * @param null|int $end exclusive upper bound (defaults to end of token stream)
     */
    protected function tokenRangeContainsTwig(Tokens $tokens, int $start, ?int $end = null): bool
    {
        $twigTypes = [Token::VAR_START_TYPE, Token::BLOCK_START_TYPE];

        return false !== $tokens->findNext($twigTypes, $start, $end);
    }

    /**
     * Return the first non-empty capture from $matches for given indexes.
     *
     * @param array<int, mixed> $matches
     */
    protected function firstMatch(array $matches, int ...$indexes): string
    {
        foreach ($indexes as $i) {
            if (array_key_exists($i, $matches)) {
                $value = $matches[$i];
                if (is_string($value) && '' !== $value) {
                    return $value;
                }
            }
        }

        return '';
    }

    /**
     * Find an associated <label for="id"> for the given id inside the
     * full template content. Returns true when a matching for= is found.
     */
    protected function hasLabelFor(string $content, string $id): bool
    {
        if ('' === $id) {
            return false;
        }

        return (bool) preg_match('/<label[^>]*for\s*=\s*["\']'.preg_quote($id, '/').'["\']/i', $content);
    }

    /**
     * Extract the first id attribute value from an opening tag content.
     */
    protected function extractFirstId(string $opening): string
    {
        if (preg_match('/\bid\s*=\s*["\']([^"\']+)["\']/i', $opening, $m)) {
            return $m[1];
        }

        return '';
    }

    /**
     * Check if an opening tag provides its own accessible name via
     * aria-labelledby or aria-label with a non-empty value.
     */
    protected function openingProvidesLabel(string $opening): bool
    {
        if (preg_match('/\baria-labelledby\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $opening, $m)) {
            $value = '' !== $m[1] ? $m[1] : ($m[2] ?? '');
            if ('' !== trim($value)) {
                return true;
            }
        }

        if (preg_match('/\baria-label\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $opening, $m)) {
            $value = '' !== $m[1] ? $m[1] : ($m[2] ?? '');
            if ('' !== trim($value)) {
                return true;
            }
        }

        return false;
    }
}
