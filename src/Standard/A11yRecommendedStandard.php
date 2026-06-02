<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigCsFixer\Rules\Node\NodeRuleInterface;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * Recommended accessibility standard — a balanced everyday ruleset.
 *
 * Extends the Basic tier with structural, ARIA, and form rules that cover
 * the most common WCAG 2.2 AA violations in real-world Twig templates.
 * This is the suggested default for most projects.
 *
 * Usage:
 *   $ruleset->addStandard(new A11yRecommendedStandard());
 */
final class A11yRecommendedStandard implements StandardInterface
{
    /**
     * @return list<RuleInterface|NodeRuleInterface>
     */
    public function getRules(): array
    {
        return StandardRuleSets::recommended();
    }
}
