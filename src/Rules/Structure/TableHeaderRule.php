<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class TableHeaderRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 !== $tokenIndex) {
            return;
        }

        $full = $this->getFullContent($tokens);

        if (!str_contains($full, '<table')) {
            return;
        }

        // Find th elements
        if (!preg_match_all('/<th\b([^>]*)>/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        foreach ($m as $set) {
            $attrs = $set[1];
            if (!preg_match('/\bscope\b\s*=\s*(?:"|\')/i', $attrs)) {
                $token = $tokens->get(0);
                $emit('Table header <th> elements should include a scope attribute.', $token, 'TableHeader.MissingScope');

                return;
            }
        }
    }
}
