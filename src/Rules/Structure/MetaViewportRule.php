<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class MetaViewportRule extends AbstractRule
{
    private bool $scanned = false;

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        if ($this->scanned) {
            return;
        }
        $this->scanned = true;

        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        // Scan full file content to avoid token split issues
        $full = '';
        foreach ($tokens->toArray() as $t) {
            $full .= $t->getValue();
        }

        $fullLower = strtolower($full);
        if (!str_contains($fullLower, 'name="viewport"') && !str_contains($fullLower, "name='viewport'")) {
            return;
        }

        if (preg_match('/user-scalable\s*=\s*no/i', $fullLower)) {
            $token = $tokens->get(0);
            $this->addError('Avoid using user-scalable=no in the viewport meta.', $token, 'MetaViewport.UserScalable');
        }
    }
}
