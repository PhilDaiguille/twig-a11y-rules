<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigCsFixer\Token\Tokens;

final class LandmarkUniqueRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        if (0 !== $tokenIndex) {
            return;
        }

        $full = strtolower($this->getFullContent($tokens));

        // common landmark names
        $landmarks = ['main', 'banner', 'navigation', 'complementary', 'contentinfo'];
        foreach ($landmarks as $land) {
            $needle = 'role="'.$land.'"';
            $count = substr_count($full, $needle);
            if ($count > 1) {
                $fake = $tokens->get(0);
                $emit(sprintf('Landmark role="%s" appears %d times; landmarks should be unique.', $land, $count), $fake, 'Landmark.Unique');

                // report only the first occurrence
                return;
            }
        }
    }
}
