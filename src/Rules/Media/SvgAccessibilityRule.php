<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Ensures SVG elements are accessible.
 *
 * - Decorative SVGs must have aria-hidden="true"
 * - Informative SVGs must have a <title> or aria-label/aria-labelledby
 * - SVGs with role="img" must have an accessible name
 *
 * @see https://www.w3.org/WAI/WCAG22/Techniques/html/H90.html
 * @see https://axe-core.github.io/rules/svg-img-alt/
 */
final class SvgAccessibilityRule extends AbstractA11yRule
{
    /**
     * Deduplicate reports for the same tag across token evaluations.
     *
     * @var array<string,bool>
     */
    private array $seenTagHashes = [];

    public function __construct()
    {
        parent::__construct(emitAsWarning: false);
    }

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // Reset deduplication hashes at the start of each new file
        if (0 === $tokenIndex) {
            $this->seenTagHashes = [];
        }

        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();

        // Quick check: ignore tokens that clearly don't contain a '<'
        if (!str_contains($value, '<')) {
            return;
        }

        // Try to collect a full tag around this token
        $fullTag = $this->collectTag($tokenIndex, $tokens, 200);

        if (!str_contains($fullTag, '>')) {
            return;
        }

        // Only act on things that look like an <svg tag
        if (!preg_match('/<\s*svg\b/i', $fullTag)) {
            return;
        }

        // Deduplicate by hashing the normalized tag text
        $normalized = preg_replace('/\s+/', ' ', trim($fullTag));
        $tagKey = md5((string) $normalized);
        if (isset($this->seenTagHashes[$tagKey])) {
            return;
        }

        $this->seenTagHashes[$tagKey] = true;

        // Check if SVG has aria-hidden="true" (decorative)
        $hasAriaHidden = preg_match('/\baria-hidden\s*=\s*["\']true["\']/i', $fullTag);
        if ($hasAriaHidden) {
            // Decorative SVG is fine
            return;
        }

        // Check for role="img"
        $hasRoleImg = preg_match('/\brole\s*=\s*["\']img["\']/i', $fullTag);

        // Check for accessible name sources
        $hasTitle = $this->hasSvgTitle($fullTag, $tokens, $tokenIndex);
        $hasAriaLabel = preg_match('/\baria-label\s*=/i', $fullTag);
        $hasAriaLabelledBy = preg_match('/\baria-labelledby\s*=/i', $fullTag);

        $hasAccessibleName = $hasTitle || $hasAriaLabel || $hasAriaLabelledBy;

        // If role="img", it MUST have an accessible name
        if ($hasRoleImg && !$hasAccessibleName) {
            $emit('SVG with role="img" is missing an accessible name (<title>, aria-label, or aria-labelledby).', $token, 'SvgA11y.MissingNameForRoleImg');

            return;
        }

        // If no aria-hidden, no role="img", and no accessible name, it's an informative SVG without a name
        if (!$hasAccessibleName) {
            $emit('SVG element is missing an accessible name. Add <title>, aria-label, aria-labelledby, or aria-hidden="true" if decorative.', $token, 'SvgA11y.MissingAccessibleName');
        }
    }

    /**
     * Check if SVG has a <title> element (either self-closing or with content).
     */
    private function hasSvgTitle(string $fullTag, Tokens $tokens, int $tokenIndex): bool
    {
        // Quick string check for self-closing title
        if (preg_match('/<\s*title\s*\/>/i', $fullTag)) {
            return true;
        }

        // Check for opening title tag
        if (preg_match('/<\s*title\s*>/i', $fullTag)) {
            return true;
        }

        // Look ahead in tokens for a title element within the SVG
        // Collect more content to find nested elements
        $extendedContent = $this->collectUntil($tokenIndex, $tokens, '</svg>', 500);

        return (bool) preg_match('/<\s*title(\s+[^>]*)?>(.*?)<\/\s*title\s*>/is', $extendedContent);
    }
}
