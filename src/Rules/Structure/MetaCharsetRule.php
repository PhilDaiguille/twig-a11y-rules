<?php

declare(strict_types=1);

namespace TwigA11y\Rules\Structure;

use TwigA11y\Rules\AbstractA11yRule;
use TwigA11y\Template\TemplateKind;
use TwigA11y\Tests\Rules\Structure\MetaCharsetRuleTest;
use TwigCsFixer\Token\Tokens;

/**
 * RGAA 8.8 / WCAG 4.1.1 — full HTML pages must declare a character encoding via
 * <meta charset="..."> or <meta http-equiv="content-type" content="text/html; charset=...">.
 *
 * @see MetaCharsetRuleTest
 * @see MetaCharsetRuleTest
 * @see MetaCharsetRuleTest
 * @see MetaCharsetRuleTest
 * @see MetaCharsetRuleTest
 * @see MetaCharsetRuleTest
 */
final class MetaCharsetRule extends AbstractA11yRule
{
    public function evaluate(Tokens $tokens, int $tokenIndex, callable $emit): void
    {
        $full = $this->getFullContent($tokens);

        $hasCharset = preg_match('/<meta\b[^>]*\bcharset\s*=/i', $full)
            || preg_match('/<meta\b[^>]*\bhttp-equiv\s*=\s*["\']content-type["\']/i', $full);

        if (!$hasCharset) {
            $emit(
                'Full-page document is missing a character encoding declaration (<meta charset="utf-8">) (RGAA 8.8, WCAG 4.1.1).',
                $tokens->get(0),
                'MissingCharset'
            );
        }
    }

    protected function evaluateOncePerFile(): bool
    {
        return true;
    }

    /** @return TemplateKind[] */
    protected function supportedKinds(): array
    {
        return [TemplateKind::FullPage];
    }
}
