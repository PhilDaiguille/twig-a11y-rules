<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

/**
 * Flags anchor elements whose visible text is a known generic phrase such as
 * "click here", "read more", or "here". These phrases provide no context to
 * assistive technology users who navigate a page link-by-link.
 *
 * WCAG 2.4.4 — Link Purpose (In Context), Level AA.
 *
 * Reported as a warning because the rule cannot reliably evaluate context-
 * dependent scenarios (e.g. a "Read more" link that is immediately preceded
 * by a descriptive heading may be acceptable).
 */
final class GenericLinkTextRule extends AbstractA11yRule
{
    /**
     * @var string[]
     */
    private array $genericPhrases = [
        'click here',
        'read more',
        'here',
        'lire la suite',
        'en savoir plus',
        'more',
        'details',
        'link',
        'cliquez ici',
    ];

    public function __construct()
    {
        parent::__construct(emitAsWarning: true);
    }

    #[\Override]
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), '<a')) {
            return;
        }

        // Match <a ...>...</a> blocks.
        $m = [];
        if (!preg_match_all('/<a\b[^>]*>(.*?)<\/a>/is', $full, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($m as $set) {
            $inner = $set[1][0];
            // Skip links that contain Twig expressions — the runtime text may
            // be descriptive.
            if (str_contains($inner, '{{')) {
                continue;
            }

            if (str_contains($inner, '{%')) {
                continue;
            }

            $text = trim(strip_tags($inner));
            if ('' === $text) {
                continue;
            }

            if (in_array(strtolower($text), $this->genericPhrases, true)) {
                $emit(
                    sprintf(
                        'Avoid generic link text "%s"; use descriptive text that explains the link destination.',
                        $text,
                    ),
                    $this->tokenAtOffset($tokens, $set[0][1], $set[0][0]),
                    'Generic',
                );
            }
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
