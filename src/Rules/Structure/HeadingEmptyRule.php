<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Structure\HeadingEmptyRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * @see HeadingEmptyRuleTest
 * @see HeadingEmptyRuleTest
 * @see HeadingEmptyRuleTest
 * @see HeadingEmptyRuleTest
 * @see HeadingEmptyRuleTest
 * @see HeadingEmptyRuleTest
 */
final class HeadingEmptyRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $token = $tokens->get($tokenIndex);

        $full = $this->getFullContent($tokens);

        preg_match_all('/<(h[1-6])[^>]*>(.*?)<\/\1>/is', $full, $m, PREG_SET_ORDER);
        foreach ($m as $set) {
            $content = trim(strip_tags($set[2]));
            if ('' === $content) {
                $emit('Heading element should not be empty.', $token, 'HeadingEmpty.Empty');

                return;
            }
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
