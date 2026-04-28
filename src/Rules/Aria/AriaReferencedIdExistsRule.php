<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AriaReferencedIdExistsRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        // Collect all ids in the template
        $idCount = preg_match_all('/\bid\s*=\s*(?:"|\')([^"\']+)(?:"|\')/i', $full, $idMatches);
        $ids = [];
        if ($idCount > 0) {
            $ids = array_flip($idMatches[1]);
        }

        // Find aria-labelledby / aria-describedby usages
        if (preg_match_all('/\baria-(?:labelledby|describedby)\s*=\s*(?:"([^"]+)"|\'([^\']+)\')/i', $full, $refs, PREG_SET_ORDER)) {
            foreach ($refs as $r) {
                $value = $r[1] ?? $r[2] ?? '';
                $pieces = preg_split('/\s+/', trim($value));
                if (false === $pieces) {
                    $pieces = [];
                }

                foreach ($pieces as $refId) {
                    if ('' === $refId) {
                        continue;
                    }

                    if (!isset($ids[$refId])) {
                        // Determine line number where the missing reference occurs
                        $pos = strpos($full, $r[0]);
                        $line = 1;
                        if (false !== $pos) {
                            $line += substr_count(substr($full, 0, $pos), "\n");
                        }

                        $fakeToken = $tokens->get(0);
                        $fakeToken = new Token(
                            $fakeToken->getType(),
                            $line,
                            1,
                            $fakeToken->getFilename(),
                            $r[0]
                        );
                        $emit(sprintf('Referenced id "%s" in aria attribute does not exist in template.', $refId), $fakeToken, 'AriaRef.MissingId');

                        // Report first missing id for determinism
                        return;
                    }
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
