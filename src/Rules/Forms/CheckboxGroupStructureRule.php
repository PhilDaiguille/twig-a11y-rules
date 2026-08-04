<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Forms;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Forms\CheckboxGroupStructureRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * WCAG 1.3.1 / RGAA 11.7 — multiple <input type="checkbox"> elements that share the
 * same name attribute must be grouped inside a <fieldset> (with a <legend>) or a
 * container with role="group" so that assistive technologies can convey the grouping.
 *
 * @see CheckboxGroupStructureRuleTest
 * @see CheckboxGroupStructureRuleTest
 * @see CheckboxGroupStructureRuleTest
 * @see CheckboxGroupStructureRuleTest
 * @see CheckboxGroupStructureRuleTest
 * @see CheckboxGroupStructureRuleTest
 */
final class CheckboxGroupStructureRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<input')) {
            return;
        }

        // Collect all <input type="checkbox" name="X"> pairs.
        if (!preg_match_all('/<input\b([^>]*)>/i', $full, $inputs, PREG_SET_ORDER)) {
            return;
        }

        /** @var array<string, int> $nameCount */
        $nameCount = [];

        foreach ($inputs as $input) {
            $attrs = $input[1];

            if (!preg_match('/\btype\s*=\s*["\']checkbox["\']/i', $attrs)) {
                continue;
            }

            if (!preg_match('/\bname\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $m)) {
                continue;
            }

            $name = '' !== $m[1] ? $m[1] : ($m[2] ?? '');
            if ('' === $name) {
                continue;
            }

            $nameCount[$name] = ($nameCount[$name] ?? 0) + 1;
        }

        foreach ($nameCount as $name => $count) {
            if ($count < 2) {
                continue;
            }

            // Build a pattern that captures the region around these checkboxes.
            // Accept both <fieldset> and role="group" containers.
            $escapedName = preg_quote($name, '/');

            // Check if all occurrences of this name appear inside a fieldset or role="group".
            $isGrouped = preg_match(
                '/<(?:fieldset\b|[^>]+\brole\s*=\s*["\']group["\'])[^>]*>.*?<input\b[^>]*\bname\s*=\s*["\']'.$escapedName.'["\'][^>]*>.*?<\/(?:fieldset|[^>]+)>/is',
                $full
            );

            if (!$isGrouped) {
                $emit(
                    sprintf(
                        'Checkbox inputs sharing name "%s" should be grouped inside a <fieldset> or a container with role="group" (WCAG 1.3.1, RGAA 11.7).',
                        $name
                    ),
                    $tokens->get(0),
                    'MissingGroup'
                );
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
