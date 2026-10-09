<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class TableHeaderRule extends AbstractA11yRule
{
    #[\Override]
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<table')) {
            return;
        }

        // Find th elements with offsets so we can emit the error at the
        // token containing the offending <th> rather than at the start of
        // the file (which makes the lint output confusing).
        $m = [];
        if (!preg_match_all('/<th\b([^>]*)>/i', $full, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        foreach ($m as $set) {
            // $set[0] is the full match [text, offset], $set[1] is the attrs
            // With PREG_OFFSET_CAPTURE these offsets always exist.
            $attrs = $set[1][0];
            $matchOffset = $set[0][1];

            // Capture scope attribute value if present
            $scopeMatch = [];
            if (!preg_match('/\bscope\b\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attrs, $scopeMatch)) {
                $emit(
                    'Table header <th> elements should include a scope attribute.',
                    $this->tokenAtOffset($tokens, $matchOffset, $set[0][0]),
                    'MissingScope',
                );
            } else {
                // Validate scope value
                $value = $this->firstMatch($scopeMatch, 1, 2, 3);
                $allowed = ['col', 'row', 'colgroup', 'rowgroup'];
                if (!in_array(strtolower($value), $allowed, true)) {
                    $emit(
                        sprintf('Table header <th> has invalid scope value "%s".', $value),
                        $this->tokenAtOffset($tokens, $matchOffset, $set[0][0]),
                        'InvalidScope',
                    );
                }
            }
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
