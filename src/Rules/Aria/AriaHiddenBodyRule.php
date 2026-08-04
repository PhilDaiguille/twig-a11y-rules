<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Aria;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigA11y\Tests\Rules\Aria\AriaHiddenBodyRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * @see AriaHiddenBodyRuleTest
 * @see AriaHiddenBodyRuleTest
 * @see AriaHiddenBodyRuleTest
 * @see AriaHiddenBodyRuleTest
 * @see AriaHiddenBodyRuleTest
 * @see AriaHiddenBodyRuleTest
 */
final class AriaHiddenBodyRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (preg_match('/<body[^>]*aria-hidden\s*=\s*(?:"|\')true(?:"|\')/i', $full)) {
            $first = $tokens->get(0);
            $emit('Do not set aria-hidden="true" on the <body> element.', $first, 'AriaHiddenBody.HiddenOnBody');
        }
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
