<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Tests\Rules\Aria\AriaErrorMessageIdExistsRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * @see AriaErrorMessageIdExistsRuleTest
 * @see AriaErrorMessageIdExistsRuleTest
 * @see AriaErrorMessageIdExistsRuleTest
 * @see AriaErrorMessageIdExistsRuleTest
 * @see AriaErrorMessageIdExistsRuleTest
 * @see AriaErrorMessageIdExistsRuleTest
 */
final class AriaErrorMessageIdExistsRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        $idCount = preg_match_all('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $full, $idMatches);
        $ids = [];
        if ($idCount > 0) {
            $ids = array_flip($idMatches[1]);
        }

        if (!preg_match_all('/\baria-errormessage\s*=\s*(?:"([^"]+)"|\'([^\']+)\')/i', $full, $refs, PREG_SET_ORDER)) {
            return;
        }

        foreach ($refs as $ref) {
            $refId = $this->firstMatch($ref, 1, 2);
            if ('' === $refId) {
                continue;
            }

            if (isset($ids[$refId])) {
                continue;
            }

            $pos = strpos($full, $ref[0]);
            $line = 1;
            if (false !== $pos) {
                $line += substr_count(substr($full, 0, $pos), "\n");
            }

            $fakeToken = $this->fakeTokenForLine($tokens, $line, $ref[0]);

            $emit(sprintf('Referenced id "%s" in aria-errormessage does not exist in template.', $refId), $fakeToken, 'AriaErrorMessage.MissingId');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
