<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Structure\FieldsetLegendRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * @see FieldsetLegendRuleTest
 * @see FieldsetLegendRuleTest
 * @see FieldsetLegendRuleTest
 * @see FieldsetLegendRuleTest
 * @see FieldsetLegendRuleTest
 * @see FieldsetLegendRuleTest
 */
final class FieldsetLegendRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains(strtolower($full), '<fieldset')) {
            return;
        }

        if (preg_match_all('/<fieldset[^>]*>(.*?)<\/fieldset>/is', $full, $m, PREG_SET_ORDER)) {
            foreach ($m as $block) {
                $inner = trim(strip_tags($block[1]));
                if ('' === $inner || !preg_match('/<legend[^>]*>\s*([^<]+?)\s*<\/legend>/i', $block[0])) {
                    $fakeToken = $tokens->get(0);
                    $emit('Fieldset must contain a non-empty <legend>.', $fakeToken, 'Fieldset.LegendMissing');

                    return;
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
