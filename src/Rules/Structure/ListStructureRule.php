<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class ListStructureRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if ($this->shouldSkipByTokenIndex($tokenIndex)) {
            return;
        }

        $full = $this->getFullContent($tokens);

        // ul/ol children must be li
        if (preg_match_all('/<ul[^>]*>(.*?)<\/ul>/is', $full, $uls, PREG_SET_ORDER)) {
            foreach ($uls as $u) {
                if (preg_match('/<(?!li)[a-z0-9]+\b/i', $u[1])) {
                    $fakeToken = $tokens->get(0);
                    $emit('List (<ul>/<ol>) contains non-<li> child.', $fakeToken, 'ListStructure.InvalidChild');

                    return;
                }
            }
        }

        // dl must have dt/dd
        if (preg_match_all('/<dl[^>]*>(.*?)<\/dl>/is', $full, $dls, PREG_SET_ORDER)) {
            foreach ($dls as $d) {
                if (!preg_match('/<dt\b/i', $d[1]) || !preg_match('/<dd\b/i', $d[1])) {
                    $fakeToken = $tokens->get(0);
                    $emit('Description list <dl> must contain <dt> and <dd>.', $fakeToken, 'ListStructure.DlMissing');

                    return;
                }
            }
        }

        // detect orphan dt/dd outside of any dl
        $withoutDl = preg_replace('/<dl[^>]*>.*?<\/dl>/is', '', $full);
        if (preg_match('/<\s*(dt|dd)\b/i', (string) $withoutDl)) {
            $fakeToken = $tokens->get(0);
            $emit('Orphan <dt> or <dd> found outside of a <dl>.', $fakeToken, 'ListStructure.OrphanDtDd');

            return;
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
