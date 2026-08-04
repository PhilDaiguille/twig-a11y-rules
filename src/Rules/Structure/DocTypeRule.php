<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigA11y\Tests\Rules\Structure\DocTypeRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * RGAA 8.1 — full HTML pages must declare a DOCTYPE.
 *
 * @see DocTypeRuleTest
 * @see DocTypeRuleTest
 * @see DocTypeRuleTest
 * @see DocTypeRuleTest
 * @see DocTypeRuleTest
 * @see DocTypeRuleTest
 */
final class DocTypeRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        if (!preg_match('/<!DOCTYPE\s+html/i', $full)) {
            $emit(
                'Full-page document is missing a <!DOCTYPE html> declaration (RGAA 8.1).',
                $tokens->get(0),
                'MissingDoctype'
            );
        }
    }

    #[\Override]
    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    /** @return TemplateKind[] */
    #[\Override]
    protected function supportedKinds(): array
    {
        return [TemplateKind::FullPage];
    }
}
