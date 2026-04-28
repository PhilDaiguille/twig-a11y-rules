<?php

declare(strict_types=1);

namespace TwigA11y\Rules;

use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

trait TokenCollectorTrait
{
    /**
     * Collect token values starting at $tokenIndex until $endPattern matches or
     * $limit tokens have been consumed.
     *
     * Uses Tokens::findNext() to skip non-TEXT tokens efficiently, falling back
     * to manual iteration only when a regex end-pattern is required.
     */
    protected function collectUntil(int $tokenIndex, Tokens $tokens, string $endPattern, int $limit = 200): string
    {
        $collected = '';
        $i = $tokenIndex;
        $end = $tokenIndex + $limit;

        while ($i <= $end && $tokens->has($i)) {
            $value = $tokens->get($i)->getValue();
            $collected .= $value;

            if (str_starts_with($endPattern, '/')) {
                if ($this->safePregMatch($endPattern, $collected)) {
                    break;
                }
            } elseif (str_contains($collected, $endPattern)) {
                break;
            }

            // Jump to the next token that has a value worth collecting.
            // Whitespace and EOL tokens between HTML attributes are skipped
            // implicitly because their values contribute to $collected without
            // needing special treatment — we only want to stop early when
            // we've collected enough.
            $next = $tokens->findNext(
                [Token::EOF_TYPE],
                $i + 1,
                $end + 1,
                true // exclude EOF — i.e. find the next non-EOF token
            );

            if (false === $next) {
                break;
            }

            $i = $next;
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
}
