<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigCsFixer\Rules\Node\NodeRuleInterface;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * Standard accessibility ruleset — comprehensive WCAG 2.2 AA coverage.
 *
 * Extends the Recommended tier with deeper checks: duplicate IDs, landmark
 * structure, table semantics, meta viewport, skip links, and more. Suitable
 * for projects targeting full WCAG 2.2 Level AA conformance.
 *
 * Usage:
 *   $ruleset->addStandard(new A11yStandard());
 */
final class A11yStandard implements StandardInterface
{
    /**
     * @return list<RuleInterface|NodeRuleInterface>
     */
    public function getRules(): array
    {
        return StandardRuleSets::standard();
    }
}
