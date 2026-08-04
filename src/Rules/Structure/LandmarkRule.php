<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigA11y\Tests\Rules\Structure\LandmarkRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * @see LandmarkRuleTest
 * @see LandmarkRuleTest
 * @see LandmarkRuleTest
 * @see LandmarkRuleTest
 * @see LandmarkRuleTest
 * @see LandmarkRuleTest
 */
final class LandmarkRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (str_contains($full, '<main') || preg_match('/role\s*=\s*["\']main["\']/i', $full)) {
            return;
        }

        $first = $tokens->get(0);
        $emit('Page should include a main landmark', $first, 'Landmark.MissingMain');
    }

    /**
     * @return TemplateKind[]
     */
    #[\Override]
    protected function supportedKinds(): array
    {
        return [TemplateKind::FullPage];
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }
}
