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

        // Find th elements
        if (!preg_match_all('/<th\b([^>]*)>/i', $full, $m, PREG_SET_ORDER)) {
            return;
        }

        $idx = 0;
        foreach ($m as $set) {
            $attrs = $set[1];
            if (!preg_match('/\bscope\b\s*=\s*(?:"|\')/i', $attrs)) {
                ++$idx;
                $token = $tokens->get(0);
                $id = 'TableHeader.MissingScope';
                if ($idx > 1) {
                    $id .= '#'.$idx;
                }

                $emit('Table header <th> elements should include a scope attribute.', $token, $id);

                // continue reporting other missing scopes
            }
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
