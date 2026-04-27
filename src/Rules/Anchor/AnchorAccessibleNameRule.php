<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Anchor;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class AnchorAccessibleNameRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        // run across tokens; tokens processed sequentially — check TEXT_TYPE tokens
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<a')) {
            return;
        }

        // collect the full tag and a chunk after it to detect nested content
        $full = $this->collectTag($tokenIndex, $tokens, 200);
        if (!str_contains($full, '>')) {
            // try to append more
            $collected = $full;
            $i = $tokenIndex + 1;
            $limit = $tokenIndex + 200;
            while ($i <= $limit && $tokens->has($i) && !str_contains($collected, '>')) {
                $collected .= $tokens->get($i)->getValue();
                ++$i;
            }

            $full = $collected;
        }

        if (!preg_match('/<\s*a\b([^>]*)>(.*?)<\s*\/\s*a\s*>/is', $full, $m)) {
            // either not a full anchor or no closing tag in collected range
            return;
        }

        $attrs = $m[1];
        $inner = strip_tags($m[2]);

        // Determine accessible name: aria-label, aria-labelledby, title, inner text, image alt
        if (preg_match('/aria-label\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $mm)) {
            $name = $mm[1] ?? $mm[2] ?? '';
            if (trim($name) === '') {
                $emit('Anchor has empty aria-label.', $token, 'Anchor.AccessibleNameEmpty');
            }

            return;
        }

        if (preg_match('/title\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $mm)) {
            $title = $mm[1] ?? $mm[2] ?? '';
            if (trim($title) !== '') {
                return; // title supplies a name
            }
        }

        // aria-labelledby references: try to resolve simple cases where the id
        // target is present literally inside the collected area
        if (preg_match('/aria-labelledby\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $mm)) {
            $ids = trim($mm[1] ?? $mm[2] ?? '');
            foreach (preg_split('/\s+/', $ids) as $id) {
                if ($id === '') {
                    continue;
                }

                // look for element with that id in the larger document
                $doc = $this->getFullContent($tokens);
                if (preg_match('/id\s*=\s*(?:"' . preg_quote($id, '/') . '"|\'' . preg_quote($id, '/') . '\')/i', $doc)) {
                    // presence is good enough for this static check
                    return;
                }
            }
        }

        // If inner text or inner textual content exists, accept it as a name
        if (trim($inner) !== '') {
            return;
        }

        // If anchor contains an <img> with alt text, that provides a name
        if (preg_match('/<\s*img\b[^>]*alt\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/is', $m[2], $imgM)) {
            $alt = $imgM[1] ?? $imgM[2] ?? ($imgM[3] ?? '');
            if (trim($alt) !== '') {
                return;
            }
        }

        // If none of the above supplied a name, report.
        $emit('Anchor element without accessible name (axe-core: link-name).', $token, 'Anchor.AccessibleName');
    }
}
