<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class TableHeaderRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<table')) {
            return;
        }

        // Find th elements with offsets so we can emit the error at the
        // token containing the offending <th> rather than at the start of
        // the file (which makes the lint output confusing).
        if (!preg_match_all('/<th\b([^>]*)>/i', $full, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        $idx = 0;
        foreach ($m as $set) {
            // $set[0] is the full match [text, offset], $set[1] is the attrs
            // With PREG_OFFSET_CAPTURE these offsets always exist.
            $attrs = $set[1][0];
            $matchOffset = $set[0][1];

            // Capture scope attribute value if present
            if (!preg_match('/\bscope\b\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attrs, $scopeMatch)) {
                ++$idx;

                // Map the match offset back to a token index so the emitted
                // error points to the token containing the <th>.
                $tokenForMatch = null;
                $pos = 0;
                foreach ($tokens->toArray() as $tIdx => $tok) {
                    $val = $tok->getValue();
                    $len = strlen($val);
                    if ($pos + $len > $matchOffset) {
                        $tokenForMatch = $tokens->get($tIdx);

                        break;
                    }

                    $pos += $len;
                }

                $token = $tokenForMatch ?? $tokens->get(0);
                $id = 'TableHeader.MissingScope';
                if ($idx > 1) {
                    $id .= '#'.$idx;
                }

                $emit('Table header <th> elements should include a scope attribute.', $token, $id);
            } else {
                // Validate scope value
                $value = $this->firstMatch($scopeMatch, 1, 2, 3);
                $allowed = ['col', 'row', 'colgroup', 'rowgroup'];
                if (!in_array(strtolower($value), $allowed, true)) {
                    // Map the match offset back to a token index so the emitted
                    // error points to the token containing the <th>.
                    $tokenForMatch = null;
                    $pos = 0;
                    foreach ($tokens->toArray() as $tIdx => $tok) {
                        $val = $tok->getValue();
                        $len = strlen($val);
                        if ($pos + $len > $matchOffset) {
                            $tokenForMatch = $tokens->get($tIdx);

                            break;
                        }

                        $pos += $len;
                    }

                    $token = $tokenForMatch ?? $tokens->get(0);
                    $emit(sprintf('Table header <th> has invalid scope value "%s".', $value), $token, 'TableHeader.InvalidScope');
                }
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
