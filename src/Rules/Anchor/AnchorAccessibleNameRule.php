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
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(Token::TEXT_TYPE)) {
            return;
        }

        $value = $token->getValue();
        if (!str_contains($value, '<a')) {
            return;
        }

        $full = $this->collectTag($tokenIndex, $tokens, 200);
        if (!str_contains($full, '>')) {
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
            return;
        }

        $attrs = $m[1] ?? '';
        $inner = isset($m[2]) ? strip_tags($m[2]) : '';

        // aria-label
        if (preg_match('/aria-label\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $mm)) {
            $name = '';
            if (isset($mm[1]) && $mm[1] !== '') {
                $name = $mm[1];
            } elseif (isset($mm[2]) && $mm[2] !== '') {
                $name = $mm[2];
            }

            if (trim($name) === '') {
                $emit('Anchor has empty aria-label.', $token, 'Anchor.AccessibleNameEmpty');
            }

            return;
        }

        // title
        if (preg_match('/title\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $mm)) {
            $title = '';
            if (isset($mm[1]) && $mm[1] !== '') {
                $title = $mm[1];
            } elseif (isset($mm[2]) && $mm[2] !== '') {
                $title = $mm[2];
            }

            if (trim($title) !== '') {
                return;
            }
        }

        // aria-labelledby
        if (preg_match('/aria-labelledby\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $mm)) {
            $ids = '';
            if (isset($mm[1]) && $mm[1] !== '') {
                $ids = $mm[1];
            } elseif (isset($mm[2]) && $mm[2] !== '') {
                $ids = $mm[2];
            }

            $parts = preg_split('/\s+/', $ids);
            if (!is_array($parts)) {
                $parts = [];
            }

            $doc = $this->getFullContent($tokens);
            foreach ($parts as $id) {
                if ($id === '') {
                    continue;
                }

                if (preg_match('/id\s*=\s*(?:"' . preg_quote($id, '/') . '"|\'' . preg_quote($id, '/') . '\')/i', $doc)) {
                    return;
                }
            }
        }

        // inner text
        if (trim($inner) !== '') {
            return;
        }

        // img alt inside anchor
        if (isset($m[2]) && preg_match('/<\s*img\b[^>]*alt\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/is', $m[2], $imgM)) {
            $alt = '';
            if (isset($imgM[1]) && $imgM[1] !== '') {
                $alt = $imgM[1];
            } elseif (isset($imgM[2]) && $imgM[2] !== '') {
                $alt = $imgM[2];
            } elseif (isset($imgM[3]) && $imgM[3] !== '') {
                $alt = $imgM[3];
            }

            if (trim($alt) !== '') {
                return;
            }
        }

        $emit('Anchor element without accessible name (axe-core: link-name).', $token, 'Anchor.AccessibleName');
    }
}
