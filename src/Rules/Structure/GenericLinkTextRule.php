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

    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), '<a')) {
            return;
        }

        // Match <a ...>...</a> blocks.
        if (!preg_match_all('/<a\b[^>]*>(.*?)<\/a>/is', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        $idx = 0;
        foreach ($m as $set) {
            $inner = $set[1];
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
                ++$idx;
                $id = 'GenericLinkText.Generic';
                if ($idx > 1) {
                    $id .= '#'.$idx;
                }

                $emit(
                    sprintf('Avoid generic link text "%s"; use descriptive text that explains the link destination.', $text),
                    $tokens->get(0),
                    $id
                );
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
