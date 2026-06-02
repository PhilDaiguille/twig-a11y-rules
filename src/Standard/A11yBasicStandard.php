<?php

declare(strict_types=1);

namespace TwigA11y\Standard;

use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Standard\StandardInterface;

/**
 * Basic accessibility standard — essential rules only.
 *
 * A minimal set covering the most critical WCAG failures (missing alt text,
 * missing labels, invalid lang attribute, etc.). Suitable as a baseline for
 * projects starting their accessibility journey.
 *
 * Usage:
 *   $ruleset->addStandard(new A11yBasicStandard());
 */
final class A11yBasicStandard implements StandardInterface
{
    /**
     * @return list<RuleInterface>
     */
    public function getRules(): array
    {
        return StandardRuleSets::basic();
    }
}
