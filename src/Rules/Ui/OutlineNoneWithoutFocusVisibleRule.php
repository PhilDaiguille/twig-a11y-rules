<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Ui;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class OutlineNoneWithoutFocusVisibleRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 !== $tokenIndex) {
            return;
        }

        $content = $this->getFullContent($tokens);
        if (!preg_match_all('/<([a-zA-Z0-9]+)([^>]*)style\s*=\s*[\"\']([^\"\']+)[\"\']/i', $content, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $match) {
            $tag = $match[1];
            $attrs = $match[2];
            $styleVal = $match[3];
            if (preg_match('/outline\s*:\s*(none|0)/i', $styleVal)) {
                $hasCompensation = false;
                if (preg_match('/class\s*=\s*[\"\']([^\"\']*)[\"\']/i', $match[0], $classMatch)) {
                    $classes = preg_split('/\s+/', strtolower(trim($classMatch[1])));
                    if (false === $classes) {
                        $classes = [];
                    }

                    foreach ($classes as $clazz) {
                        if ('focus-visible' === $clazz) {
                            $hasCompensation = true;

                            break;
                        }
                    }
                }

                if (!$hasCompensation) {
                    // Calcul la ligne d’apparition pour l’erreur
                    $ligne = 1;
                    $pos = strpos($content, $match[0]);
                    if (false !== $pos) {
                        $ligne += substr_count(substr($content, 0, $pos), "\n");
                    }

                    $fakeToken = $tokens->get(0);
                    $fakeToken = new Token(
                        $fakeToken->getType(),
                        $ligne,
                        1,
                        $fakeToken->getFilename(),
                        $match[0]
                    );
                    $emit('Usage de outline:none/0 sans compensation focus-visible', $fakeToken, 'OutlineNoneNoFocusVisible');
                }
            }
        }
    }

    protected function emitsWarnings(): bool
    {
        return true;
    }
}
