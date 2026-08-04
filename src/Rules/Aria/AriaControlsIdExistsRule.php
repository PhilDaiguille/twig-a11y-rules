<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Aria\AriaControlsIdExistsRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * @see AriaControlsIdExistsRuleTest
 * @see AriaControlsIdExistsRuleTest
 * @see AriaControlsIdExistsRuleTest
 * @see AriaControlsIdExistsRuleTest
 * @see AriaControlsIdExistsRuleTest
 * @see AriaControlsIdExistsRuleTest
 */
final class AriaControlsIdExistsRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, 'aria-controls')) {
            return;
        }

        $idCount = preg_match_all('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $full, $idMatches);
        $ids = [];
        if ($idCount > 0) {
            $ids = array_flip($idMatches[1]);
        }

        if (!preg_match_all('/\baria-controls\s*=\s*(?:"([^"]+)"|\'([^\']+)\')/i', $full, $refs, PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($refs[0] as $index => $match) {
            $attr = $match[0];
            $offset = $match[1];
            $value = $refs[1][$index][0] ?: $refs[2][$index][0];
            $pieces = preg_split('/\s+/', trim($value));

            if (false === $pieces) {
                $pieces = [];
            }

            foreach ($pieces as $refId) {
                if ('' === $refId) {
                    continue;
                }

                if (isset($ids[$refId])) {
                    continue;
                }

                $line = 1 + substr_count(substr($full, 0, $offset), "\n");
                $fakeToken = $this->fakeTokenForLine($tokens, $line, $attr);

                $emit(sprintf('Referenced id "%s" in aria-controls does not exist in template.', $refId), $fakeToken, 'AriaControls.MissingId');

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
