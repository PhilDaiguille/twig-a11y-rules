<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Media;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class RoleImgAltRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);
        if (false === stripos($full, 'role="img"')) {
            return;
        }

        // Capture the full SVG block (opening tag + inner content + closing tag)
        if (preg_match_all('/<svg\b([^>]*)role\s*=\s*(?:"|\')img(?:"|\')[^>]*>(.*?)<\/svg>/is', $full, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                // $m[2] is the inner content of the <svg> element
                if (!preg_match('/<title[^>]*>\s*[^<]+\s*<\/title>/i', $m[2])) {
                    $fakeToken = $tokens->get(0);
                    $emit('SVG with role="img" must include a <title>.', $fakeToken, 'RoleImg.MissingTitle');

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
